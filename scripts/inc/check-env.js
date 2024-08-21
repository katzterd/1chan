import log from '#inc/logger.js'

export default function checkEnv(params, callback=null) {
	params.forEach(param => {
		if (param instanceof Array) {
			if (!param.find(opt => (typeof process.env[opt] !== 'undefined'))) {
				log.fatal(`По крайней мере один параметр из списка [${param.join(', ')}] должен быть определен в .env-файле!`)
			}
		}
		else {
			if (typeof process.env[param] === 'undefined') {
				log.fatal(`Параметр «${param}» не определен в .env-файле!`)
			}
			if (callback) callback(param)
		}
	})
}