// import { createHmac } from 'node:crypto'
import { cacheGet, cacheSet } from '#inc/kvs.js'

const cacheKey = `ControlModel::mods`

export async function createAdmin(user, pass) {
	// const hash = createHmac('sha256', pass + process.env.SALT)
	const mod = [{
		name: user,
		key: /*hash*/ pass,
		class: '0',
		category: '*'
	}]
	return await cacheSet(cacheKey, mod)
}

export async function listAdmins() {
	return await cacheGet(cacheKey)
}