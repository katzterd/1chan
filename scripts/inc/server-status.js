import log from '#inc/logger.js'
import axios from 'axios'
import { kvsConnection, cacheGet, cacheSet } from '#inc/kvs.js'

export async function updateServerStatus() {
	const kvs = await kvsConnection()
	const blogLinks = await cacheGet(`Blog_BlogLinksModel::links`)
	for (let section in blogLinks) {
		for (let link of blogLinks[section]) {
			link.offline = await isOffline(link.href)
		}
	}
	cacheSet(`Blog_BlogLinksModel::links`, blogLinks)

	const onlineLinks = await kvs.lRange(`Blog_BlogOnlineModel::links`, 0, -1)
	for (let oLink of onlineLinks) {
		const current = await cacheGet(`Blog_BlogOnlineModel:links:${oLink}`)
		const offline = await isOffline(current.link)
		if (current && !offline) continue;
		const result = await kvs.lRem(`Blog_BlogOnlineModel::links`, 0, oLink)
		if (result === 1) {
			log.timed.info(`Ссылка ${current.link} удалена из Онлайна`)
		}
	}

	// log.info(`[${new Date()}] Статус серверов обновлен`)
}

async function isOffline(url) {
	try {
		const response = await axios.head(url, { 
			proxy: process.env.SERVER_STATUS_PROXY,
			validateStatus: () => true,
			timeout: process.env.SERVER_STATUS_TIMEOUT
		})
		return +!~[200, 301, 302, 303].indexOf(response.status)
	}
	catch(e) {
		return 1
	}
}
