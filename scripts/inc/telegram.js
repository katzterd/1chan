import { Telegram } from 'telegraf'
import { texyToTgMarkdown, escapeSpecialChars, escapeInnerURL, findMediaInText } from '#inc/markup.js'
import log from '#inc/logger.js'
import checkEnv from '#inc/check-env.js'

let tg = null
if (process.env?.TG_ENABLE) {
	checkEnv([["TG_CHANNEL_ALL", "TG_CHANNEL_APPROVED"], "TG_BOT_TOKEN", "WEB_DOMAIN"])
	tg = new Telegram(process.env.TG_BOT_TOKEN)
}

function sendMessage(channel, text, media=null) {
	if (! tg) {
		log.warn('Постинг в Telegram-канал отключен')
		return
	}
	const chan = (channel=='approved')
		? process.env?.TG_CHANNEL_APPROVED
		: process.env?.TG_CHANNEL_ALL
	if (chan) {
		try {
			if (media) {
				tg['send' + (media.isVideo ? 'Video' : 'Photo')](chan, media.src, { caption: text, parse_mode: "MarkdownV2"})
			}
			else {
				tg.sendMessage(chan, text, { parse_mode: "MarkdownV2" })
			}
		}	catch(e) {
			log.err(e)
		}
	}
}

export default async function telegramSrvPlugin(fastify) {
	fastify.post("/publish/", async (req, reply) => {
		const { id, category, link, title, text_original, text_full, channel } = req.body
		const titleEscaped = escapeSpecialChars(title)
		const [media, text_cut] = findMediaInText(text_original)
		console.log(media)
		const text = texyToTgMarkdown(text_cut)
		const internal_link = escapeInnerURL(`${process.env.WEB_DOMAIN}/news/res/${id}`)
		const link_esc = escapeInnerURL(link)
		const url_title = `*[${titleEscaped}](${link ? link_esc : internal_link})*`
		const read_more = `[Читать${text_full ? ' дальше' :''}](${internal_link})`

		const msgText = url_title + '\n\n' + text + '\n\n' + read_more
		
		sendMessage(channel, msgText, media)
	})
}