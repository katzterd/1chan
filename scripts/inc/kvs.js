import log from '#inc/logger.js'
import { createClient } from 'redis'
import { serialize, unserialize } from 'php-serialize'
import checkEnv from '#inc/check-env.js'

checkEnv(["REDIS_HOST", "REDIS_PORT"])

let kvs = null
export async function kvsConnection() {
	if (! kvs) {
		kvs = await createClient({
			url: `redis://${process.env.REDIS_HOST}:${process.env.REDIS_PORT}`
		})
		.on('error', err => {
			log.err('Node <-> Redis connection error')
			console.error(err)
		})
		.on('ready', () => log.succ(`🔺 Подключен к серверу redis: ${process.env.REDIS_HOST}:${process.env.REDIS_PORT} \n`))
		.connect()
	}
	return kvs
}

export async function cacheGet(key) {
	const kvs = await kvsConnection()
	const raw = await kvs.get(key)
	return raw ? unserialize(raw) : null
}

export async function cacheSet(key, val) {
	const kvs = await kvsConnection()
	return kvs.set(key, serialize(val))
}
