
// SPOOKY REGULAR EXPRESSIONS AHEAD - YOU HAVE BEEN WARNED.

import { html } from 'telegram-format'
import { toMarkdownV2 as toMarkdownV2original } from "@telegraf/entity"

export function findMediaInText(str) {
	let media = null
	str = str.replace(/[\s]*\[(i|c):([a-z0-9]{5,})(?:\.(jpe?g|gif|png|webp|webm|mp4))?:\][\s]*/i, (_, svc, code, ext) => {
		const isVideo = ['webm','mp4'].includes(ext)
		, src = (svc == 'i')
			? `https://i.imgur.com/${code}${isVideo ? '.'+ext : '.jpg'}`
			: `https://files.catbox.moe/${code}.${ext}`
		media = {isVideo, src}
		return ''
	})
	return [media, str]
}

// https://core.telegram.org/bots/api#markdownv2-style
const specialChars = /(?<!\\)([_\*\[\]\(\)~`>#\+\-=\|{}\.!])/gm

export function texyToTgMarkdown(str) {
	const pc = new PrivateChars()

	str = str
	// Code block
	.replace(/^\/---(\n.+)^\\---$/gms, (_, match) => {
		match = match.replace(/([\\`])/gm, '\\$1')
		return pc.add('```' + match + '```')
	})
	// Inline code
	.replace(/(?<!\\)`(.+?)`/gm, (_, match) => {
		match = match.replace(/([\\`])/gm, '\\$1') // Inside pre and code entities, all '`' and '\' characters must be escaped with a preceding '\' character.
		return pc.add('`' + match + '`')
	})
	// Bold
	.replace(/(?<!\\)\*\*(.+?)\*\*(?!\*)/gms, pc.makeReplacer('*'))
	// Italic
	.replace(/(?<!\\)\*(.+?)(?<!\\)\*/gms, pc.makeReplacer('_'))
	// Spoiler
	.replace(/(\\)?%%(.+?)(\\)?%%/gms, (_, bs1, content, bs2) => {
		const bars = pc.add('||')
		return (bs1 ? '\\\\' : '') + bars + content + bars + (bs2 ? '\\\\' : '')
	} )
	// Link with text
	.replace(/"(.+)":(https?:\/\/[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&//=]*[-a-zA-Z0-9()@%_\+~#&//=])?)/gm,
		(_, text, url) =>
			// Inside the (...) part of the inline link and custom emoji definition, all ')' and '\' must be escaped with a preceding '\' character.
			pc.add('[') + text + pc.add(']') + pc.add('(') + pc.add(escapeInnerURL(url)) + pc.add(')')	)

	// In all other places characters '_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!' must be escaped with the preceding character '\'.
	return pc.decode(escapeSpecialChars(str))
}

export function escapeInnerURL(url) {
	return url.replace(/(?<!\\)([\)\\])/gm, '\\$1')
}

export function escapeSpecialChars(str) {
	return str.replace(specialChars, '\\$1')
}

// Temporarily replacing strings with unique characters from the private space
class PrivateChars {
	initialStartPoint = 0xE000

	constructor(startPoint = this.initialStartPoint) {
		this.chars = new Set()
		this.startPoint = startPoint
		this.replacers = {}
	}

	add(str, returnIndex=false) {
		var i
		if (this.chars.has(str))
			i = [...this.chars].indexOf(str)
		else {
			this.chars.add(str)
			i = this.chars.size - 1
		}
		const ch = String.fromCharCode(this.startPoint + i)
		return returnIndex ? [ch, this.startPoint + i] : ch
	}

	encode(str) {
		const chars = [...this.chars]
		const exp = new RegExp(chars.map(ch => _escapeRegExp(ch)).join('|'), 'g')
		return str.replace(exp, match => String.fromCharCode(this.startPoint + chars.indexOf(match)))
	}

	getRange() {
		return this.formatU(this.startPoint) 
			+ '-' 
			+ this.formatU(this.startPoint + this.chars.size)
	}

	decode(str) {
		const chars = [...this.chars]
		const exp = new RegExp(`[${this.getRange()}]`, 'g')
		return str.replace(exp, match => chars[match.charCodeAt(0) - this.startPoint])
	}

	formatU(num) {
		return '\\u' + num.toString(16).padStart(4, '0')
	}

	makeReplacer(tag) {
		if (! this.replacers[tag]) {
			this.replacers[tag] = (_, match) => {
				const bt = this.add(tag)
				return bt + match + bt
			}
		}
		return this.replacers[tag]
	}
}

export function newlines2br(txt) {
	return txt.replace(/\n{2,}/g, '<br><br>').replace(/\n/g, '<br>')
}

export function TgEntitiesToHTML(text, ctx) {
	const entities = ctx.message?.entities || ctx.message?.caption_entities || []
	let formattedText = ''
	let lastOffset = 0
	entities.forEach(entity => {
		// Add text before the entity
		formattedText += newlines2br(html.escape(text.slice(lastOffset, entity.offset)))
		// Get the entity content
		const entityTextRaw = text.slice(entity.offset, entity.offset + entity.length)
		, entityTextPre = html.escape(entityTextRaw)
		, entityText = newlines2br(entityTextPre)
		switch (entity.type) {
			case 'blockquote': 
				formattedText += `<q>${entityText}</q>`
				break;
			case 'bold':
				formattedText += `<b>${entityText}</b>`
				break;
			case 'italic':
				formattedText += `<i>${entityText}</i>`
				break;
			case 'code':
				formattedText += `<code>${entityText}</code>`
				break;
			case 'pre':
				formattedText += `<pre>${entityTextPre}</pre>`
				break;
			case 'underline':
				formattedText += `<u>${entityText}</u>`
				break;
			case 'strikethrough':
				formattedText += `<s>${entityText}</s>`
				break;
			case 'spoiler':
				formattedText += `<span class="b-spoiler-text">${entityText}</span>`
				break;
			case 'text_link':
				formattedText += `<a href="${entity.url}" rel="nofollow">${entityText}</a>`
				break;
			case 'url':
				formattedText += `<a href="${entity.url}" rel="nofollow">${entityText}</a>`
				break;
			default:
				formattedText += entityText;
		}
		lastOffset = entity.offset + entity.length
	})
	formattedText += newlines2br(html.escape(text.slice(lastOffset)))
	return [`<p>${formattedText}</p>`, {text, entities}]
}

export function removeSymbolsFromString(str) {
	return str.replace(/[^\p{L}\p{N}\s]/gu, '')
}

export function htmlspecialchars(str) {
	return str.replace(/[&<>"']/g, function(match) {
		return {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		}[match]
	})
}

// Properly working toMarkdownV2 (see https://github.com/telegraf/entity/issues/13)
export function toMarkdownV2(message) {
	if (message?.entities && message.entities.length) {
		return toMarkdownV2original(message)
	}
	else {
		return escapeSpecialChars(message.text)
	}
}