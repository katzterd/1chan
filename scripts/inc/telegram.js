import { Telegram } from 'telegraf'
import { texyToTgMarkdown, escapeSpecialChars, escapeInnerURL, findMediaInText } from '#inc/markup.js'
import log from '#inc/logger.js'
import checkEnv from '#inc/check-env.js'
import { kvsConnection } from "#inc/kvs.js"

let tg = null
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
	let { id, category, link, title, text_original, text_full, channel } = data
	title = escapeSpecialChars(title)
	let [media, text] = findMediaInText(text_original)
	text = texyToTgMarkdown(text)
	const internal_link = escapeInnerURL(`${process.env.WEB_DOMAIN}/news/res/${id}`)
	const external_link = escapeInnerURL(link)
	const url_title = `*[${title}](${link ? external_link : internal_link})*`
	const read_more = `[Читать${text_full ? ' дальше' :''}](${internal_link})`

	// Формирование сообщения
	const msgText = url_title + '\n\n' + text + '\n\n' + read_more
	
	// Отправка сообщения
	let msg = null
	if (media) {
		try {
			msg = await tg['send' + (media.isVideo ? 'Video' : 'Photo')](process.env.TG_CHANNEL_ALL, media.src, { caption: msgText, parse_mode: "MarkdownV2"})
		}	catch(e) {
			log.err(`Ошибка при отправке медиафайла (${media.src}) в канал`, e)
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
			sendMessage(req.body)
		}
	})
}