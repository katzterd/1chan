import { Telegram, Markup, Telegraf, Scenes, session } from 'telegraf'
import { Redis } from "@telegraf/session/redis"
import { 
	texyToTgMarkdown, 
	escapeSpecialChars, 
	escapeInnerURL, 
	findMediaInText, 
	TgEntitiesToHTML, 
	removeSymbolsFromString,
	htmlspecialchars
} from '#inc/markup.js'
import log from '#inc/logger.js'
import checkEnv from '#inc/check-env.js'
import { kvsConnection } from "#inc/kvs.js"
import SQL from 'sql-template-strings'
import { connectionPool as sql } from '#inc/database.js'
import { createHash } from 'crypto'
import { toMarkdownV2 } from "@telegraf/entity"
import axios from 'axios'
import { promises as fs } from 'fs'
import fsSync from 'fs'
import path from 'path'

let tg = null, bot = null
if (process.env?.TG_ENABLE) {
	checkEnv(["TG_CHANNEL_ALL", "TG_BOT_TOKEN", "WEB_DOMAIN"])
	tg = new Telegram(process.env.TG_BOT_TOKEN)
}

async function sendMessage(data) {
	if (! tg) {
		log.warn('Постинг в Telegram-канал отключен')
		return
	}

	// Обработка данных
	let { id, category, link, title, text_original, text_full, origin, media } = data
	let text = null
	if (origin == 'web') {
		title = escapeSpecialChars(title.trim());
		[media, text] = findMediaInText(text_original)
		text = texyToTgMarkdown(text.trim())
	}
	else {
		text = toMarkdownV2(text_original)
		if (media)
			media = {isVideo: false, src: media}
	}
	
	const internal_link = escapeInnerURL(`${process.env.WEB_DOMAIN}/news/res/${id}`)
	const external_link = escapeInnerURL(link)
	const url_title = `[${(link ? '↗️ ' : '') + title}](${link ? external_link : internal_link})`
	const btm_text = []
	if (external_link || text_full)
		btm_text.push(`[Читать${text_full ? ' дальше' :''}](${internal_link})`)

	// Получение категории
	if (category) {
		const [record, _] = await sql.query(SQL`SELECT title FROM 1chan_category WHERE id = ${category}`)
		if (record.length) {
			// Убрать из имени пробелы и пунктуацию для создания хэштега
			const catName = record[0].title.replace(/[^\p{L}\p{N}\s]/ug, '').replaceAll(' ', '_')
			btm_text.unshift(escapeSpecialChars('#' + catName))
		}
	}

	// Формирование сообщения
	const msgText = 
		'__*' + url_title + '*__' + 
		'\n\n' + 
		text + 
		(btm_text 
			? '\n\n' + btm_text.join(' \\| ') 
			: '')

	// Отправка сообщения
	let msg = null
	if (media) {
		try {
			msg = await tg['send' + (media.isVideo ? 'Video' : 'Photo')](process.env.TG_CHANNEL_ALL, media.src, { caption: msgText, parse_mode: "MarkdownV2" })
		}	catch(e) {
			log.err(`Ошибка при отправке медиафайла (id=${media.src}) в канал`, e)
			media = null // При невозможности загрузить изображение, будет отправлен только текст
		}
	}
	if (!media) {
		try {
			msg = await tg.sendMessage(process.env.TG_CHANNEL_ALL, msgText, { parse_mode: "MarkdownV2" })
		}	catch(e) {
			log.err("Ошибка при отправке поста в канал", e)
		}
	}

	// Сохранение ID сообщения
	if (msg?.message_id) {
		saveMessageID(id, msg.message_id)
	}
	return msg
}


async function saveMessageID(id, msg_id) {
	const kvs = await kvsConnection()
	kvs.set(`Telegram:id:${id}`, msg_id)
}

async function getMessageID(id) {
	const kvs = await kvsConnection()
	return await kvs.get(`Telegram:id:${id}`)
}

async function forwardApprovedMessage(id) {
	if (!tg || !process.env?.TG_CHANNEL_APPROVED) {
		log.warn('Постинг в TG-канал одобренного отключен')
		return
	}
	const msg_id = await getMessageID(id)
	if (msg_id) {
		try {
			await tg.forwardMessage(process.env.TG_CHANNEL_APPROVED, process.env.TG_CHANNEL_ALL, msg_id)
		} catch(e) {
			log.err(e)
		}
	}
}

export default async function telegramSrvPlugin(fastify) {
	fastify.post("/publish/", async (req, reply) => {
		if (req.body?.approve && req.body?.id) {
			forwardApprovedMessage(req.body.id)
		}
		else {
			sendMessage({...req.body, origin: 'web'})
		}
	})
}

if (process.env?.TG_ENABLE && process.env?.TG_FORWARDING_ENABLE) {
	checkEnv(["TG_FORWARDING_COOLDOWN", "REDIS_HOST", "REDIS_PORT", "TITLE_MIN_LENGTH", "TITLE_MAX_LENGTH", "TEXT_MIN_LENGTH", "TEXT_MAX_LENGTH", "FULL_MAX_LENGTH", "MD5_SALT"])
	const bot = new Telegraf(process.env.TG_BOT_TOKEN)
	const store = Redis({ url: `redis://${process.env.REDIS_HOST}:${process.env.REDIS_PORT}` })
	const kvs = await kvsConnection()
	
	const createPostScene = new Scenes.WizardScene(
		'add-news-entry',
		// 1) (Начало) → Категория?
		async (ctx) => {
			const [cats, _] = await sql.query(SQL`SELECT id, title FROM 1chan_category`)
			ctx.wizard.state.cats = [{id: 0, title: 'Без категории'}, ...cats]
			await ctx.reply('Выберите категорию', {
				reply_markup: {
					keyboard: ctx.wizard.state.cats.map(c => [{text: c.title}]),
					one_time_keyboard: true,
        	resize_keyboard: true
				}
			})
			return ctx.wizard.next()
		},
		// 2) Категория. → Ссылка?
		async (ctx) => {
			const cat = ctx.wizard.state.cats.find(c => c.title == ctx.message?.text)
			if (! cat) {
				await ctx.reply('Неверная категория. Выберите из списка.', {
					reply_markup: {
						keyboard: ctx.wizard.state.cats.map(c => [{text: c.title}]),
						one_time_keyboard: true,
						resize_keyboard: true
					}
				})
				return
			}
			ctx.wizard.state.category = cat.id
			await ctx.reply('Введите ссылку на источник', {
				reply_markup: { 
					remove_keyboard: true,
					inline_keyboard: [[{ text: 'Пропустить', callback_data: 'skip_link' }]]
				}
			})
			return ctx.wizard.next()
		},
		// 3) Ссылка. → Заголовок?
		async (ctx) => {
			if (ctx.callbackQuery) {
				await ctx.answerCbQuery()
				ctx.wizard.state.link = null
				await ctx.reply('Введите заголовок новости')
				return ctx.wizard.next()
			}
			const link = ctx?.message?.text
			if (!link || !link.match(/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/)) {
				await ctx.reply('Введите валидную ссылку', {
					reply_markup: { 
						inline_keyboard: [[{ text: 'Пропустить', callback_data: 'skip_link' }]]
					}
				})
				return
			}
			else if (link.length > 255) {
				await ctx.reply('Длина ссылки не должна превышать 255 символов', {
					reply_markup: { 
						inline_keyboard: [[{ text: 'Пропустить', callback_data: 'skip_link' }]]
					}
				})
				return
			}
			else {
				ctx.wizard.state.link = htmlspecialchars(link)
				await ctx.reply('Введите заголовок новости')
				return ctx.wizard.next()
			}
		},
		// 4) Заголовок. → Текст?
		async (ctx) => {
			let topicName = ctx.message?.text
			if (!topicName) {
				await ctx.reply(`Заголовок не введён.`)
				return
			}
			topicName = topicName.replace(/\n/g, ' ')
			if (
				topicName.length < process.env.TITLE_MIN_LENGTH
				||
				topicName.length > process.env.TITLE_MAX_LENGTH
			) {
				await ctx.reply(`Заголовок должен содержать от ${process.env.TITLE_MIN_LENGTH} до ${process.env.TITLE_MAX_LENGTH} символов`)
				return
			}
			if (removeSymbolsFromString(topicName) == '') {
				await ctx.reply(`Заголовок не несёт смысловой нагрузки`)
				return
			}
			if (topicName.substr(-1,1) == '.') {
				await ctx.reply(`Точка в конце заголовка`) // Yes we will be anal about it
				return
			}
			ctx.wizard.state.topicName = htmlspecialchars(topicName)
			await ctx.reply('Введите текст новости (можно с картинкой)')
    	return ctx.wizard.next()
		},
		// 5) Текст. → ЭкстраТекст?
		async (ctx) => {
			const text = ctx.message?.text || ctx.message?.caption
			if (!text) {
				await ctx.reply('Сообщение не содержит текста')
				return
			}
			if (
				text.length < process.env.TEXT_MIN_LENGTH
				||
				text.length > process.env.TEXT_MAX_LENGTH
			) {
				await ctx.reply(`Текст новости должен содержать от ${process.env.TEXT_MIN_LENGTH} до ${process.env.TEXT_MAX_LENGTH} символов`)
				return
			}
			[ctx.wizard.state.mainContent, ctx.wizard.state.mainContentRaw] = TgEntitiesToHTML(text, ctx)
			if (ctx.message?.photo) {
				const photo = ctx.message.photo.pop()
				ctx.wizard.state.image = photo.file_id
				const file = await ctx.telegram.getFile(photo.file_id)
				ctx.wizard.state.imageTempFile = `https://api.telegram.org/file/bot${process.env.TG_BOT_TOKEN}/${file.file_path}`
			}
			await ctx.reply(
				'Введите подробную часть сообщения', {
					reply_markup: { 
						inline_keyboard: [[{ text: 'Пропустить', callback_data: 'skip_extra' }]]
					}
				}
			)
			return ctx.wizard.next()
		},
		// 6) ЭкстраТекст. → (Конец)
		async (ctx) => {
			if (ctx.callbackQuery) {
				await ctx.answerCbQuery()
				ctx.wizard.state.extraContent = null
			}
			else {
				const text = ctx?.message?.text || ctx?.message?.caption
				if (text) {
					if (text.length > process.env.FULL_MAX_LENGTH) {
						await ctx.reply(`Текст новости должен содержать не более ${process.env.FULL_MAX_LENGTH} символов`)
						return
					}
					[ctx.wizard.state.extraContent, ctx.wizard.state.extraContentRaw] = TgEntitiesToHTML(text, ctx)
				}
				else {
					ctx.wizard.state.extraContent = null
				}
				if (ctx.message?.photo) {
					const photo = ctx.message.photo.pop()
					ctx.wizard.state.extraImage = photo.file_id
					const file = await ctx.telegram.getFile(photo.file_id)
					ctx.wizard.state.extraImageTempFile = `https://api.telegram.org/file/bot${process.env.TG_BOT_TOKEN}/${file.file_path}`
				}
			}
			return ctx.wizard.steps[ctx.wizard.cursor + 1](ctx)
		},
		// 7) Конец
		async (ctx) => {
			
			const id = await kvs.incr('Blog_BlogPostsModel::nextPostId')
			const ip = createHash('md5').update(ctx.from.id + process.env.MD5_SALT).digest('hex')
			const now = php_now()
			const category = ctx.wizard.state.category
			const link = ctx.wizard.state?.link ?? ''
			const title = ctx.wizard.state.topicName
			const text_full = ctx.wizard.state?.extraContent ?? ''

			ctx.session.lastSubmission = now

			// Send the message to the channel
			const msg = await sendMessage({
				id,
				category,
				link,
				title,
				text_original: ctx.wizard.state.mainContentRaw,
				text_full,
				origin: 'bot',
				media: ctx.wizard.state?.image
			})

			// Download and store the images if exist
			if (ctx.wizard.state.imageTempFile) {
				try {
					let src = await downloadImage(ctx.wizard.state.imageTempFile)
					if (src) {
						src = `/uploads/telegram/${src}`
						ctx.wizard.state.mainContent = `<a target="_blank" class="b-image-link" rel="nofollow noopener noreferrer"
							href="${src}">
							<img src="${src}" alt="" /></a>` + ctx.wizard.state.mainContent
					}
						
				} catch(e) {console.log(e)}
			}
			if (text_full && ctx.wizard.state.extraImageTempFile) {
				try {
					const src = await downloadImage(ctx.wizard.state.extraImageTempFile)
					if (src) {
						src = `/uploads/telegram/${src}`
						text_full = `<a target="_blank" class="b-image-link" rel="nofollow noopener noreferrer"
							href="${src}">
							<img src="${src}" alt="" /></a>` + text_full
					}
				} catch(e) {console.log(e)}
			}

			// Store the message in the database
			try {
				const insertion = await sql.execute(
					SQL`INSERT INTO 1chan_post (
						id,
						ip,
						category,
						created_at,
						updated_at,
						link,
						title,
						text,
						text_full,
						rate,
						author
					) VALUES (
						${id},
						${ip},
						${category},
						${now},
						${now},
						${link},
						${title},
						${ctx.wizard.state.mainContent},
						${text_full},
						0,
						"telegram"
					)`)
					await ctx.reply('Пост отправлен!')
			} catch(e) {
				log.err('Ошибка при отправке поста', e)
				await ctx.reply('Ошибка при отправке поста!')
			} finally {
				return ctx.scene.leave()
			}
		}
	)

	bot.use(session({ 
		store, 
		getSessionKey: (ctx) => ctx.from?.id.toString() 
	}))
	const stage = new Scenes.Stage([createPostScene])

	const commands = [
		{ command: 'post', description: 'Отправить новость' },
		{ command: 'cancel', description: 'Отменить отправку' }
		// ... other commands
	]
	bot.telegram.setMyCommands(commands)

	bot.command('cancel', async (ctx) => {
		const sceneID = ctx.session?.__scenes?.current
		if (sceneID) {
			await stage.scenes.get(sceneID).leaveHandler(ctx, () => {})
			ctx.session.__scenes = {
				current: undefined,
				state: {},
				expires: undefined
			}
			return ctx.reply('Операция отменена', Markup.removeKeyboard())
		}
		return ctx.reply('Нечего отменять')
	})
	
	// Crucial step! Won't work without it don't ask why
	bot.use((ctx, next) => {
		// Preserve scene system
		if (ctx?.session && !ctx.session?.__scenes) {
			ctx.session.__scenes = { current: undefined, state: {} };
		}
		return next();
	});
	
	bot.use(stage.middleware())

	bot.command('post', async (ctx) => {
		const now = php_now()
		
		if (ctx.session.lastSubmission) {
			const cooldownEnd = ctx.session.lastSubmission + (process.env.TG_FORWARDING_COOLDOWN * 60)
			if (now < cooldownEnd) {
				const secondsLeft = Math.ceil(cooldownEnd - now)
				return ctx.reply(`Таймаут ${secondsLeft} с.`)
			}
		}
		
		return ctx.scene.enter('add-news-entry')
	})

	bot.launch()

	process.once('SIGINT', () => bot.stop('SIGINT'));
	process.once('SIGTERM', () => bot.stop('SIGTERM'));
}

// Ensure storage directory exists
async function ensureDirectory(dir) {
	try {
		await fs.mkdir(dir, { recursive: true })
	} catch (err) {
		if (err.code !== 'EEXIST') throw err
	}
}

async function downloadImage(fileUrl) {
	const dir = '../www/uploads/telegram'
	await ensureDirectory(dir)
	const fileExt = path.extname(fileUrl) || '.jpg'
	const fileName = Math.floor(Date.now() / 1000) + Math.floor(Math.random() * 100001) + fileExt
	const filePath = path.join(dir, fileName)
	const response = await axios({
		method: 'GET',
		url: fileUrl,
		responseType: 'stream'
	})
	const writer = fsSync.createWriteStream(filePath)
	response.data.pipe(writer)
	return await new Promise((resolve, reject) => {
		writer.on('finish', resolve(fileName))
		writer.on('error', (e) => {
			log.err(e)
			reject()
		})
	})
}

function php_now() {
	return Math.floor(Date.now() / 1000)
}