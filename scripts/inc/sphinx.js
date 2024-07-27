import { promises as fs } from 'fs'
import log from '#inc/logger.js'
import * as cp from 'node:child_process'
import findProcess from 'find-process'
import sleep from '#inc/sleep.js'

export async function indexerInit() {
	let indexerConfig = await fs.readFile('1chan.template.conf', 'utf8');
	[ "SQL_HOST",
		"SQL_USER",
		"SQL_PASSWORD",
		"SQL_NAME",
		"SQL_PORT",
		"SPHINX_DATA_DIR",
		"SPHINX_BIN_DIR",
		"INDEXER_MEM_LIMIT",
		"INDEXER_MAX_IOPS",
		"SEARCHD_IP_PORT",
		"SEARCHD_LOG_FILE",
		"SEARCHD_READ_TIMEOUT",
		"SEARCHD_MAX_CHILDREN",
		"SEARCHD_PID_FILE"
	].forEach(param => {
		if (typeof process.env[param] === 'undefined') {
			log.fatal(`Параметр «${param}» не определен в .env-файле!`)
		}
		indexerConfig = indexerConfig.replaceAll(`<${param}>`, process.env[param])
	})

	await fs.writeFile('1chan.generated.conf', indexerConfig)
	log.succ('Файл конфигурации Sphinx сгенерирован')

	;['searchd', 'indexer'].forEach(async (bin) => {
		const filename = bin + (process.platform == 'win32' ? '.exe' :'')
		const exists = await checkExistance(`${process.env.SPHINX_BIN_DIR}/${filename}`)
		if (! exists) {
			log.fatal(`Файл «${filename}» не найден в ${process.env.SPHINX_BIN_DIR}!`)
		}
	})

	if (! (await checkExistance(process.env.SPHINX_DATA_DIR))) {
		await fs.mkdir(process.env.SPHINX_DATA_DIR)
		log.succ(`Директория данных Sphinx создана: ${process.env.SPHINX_DATA_DIR}`)
	}
	else {
		log.succ(`Директория данных Sphinx: ${process.env.SPHINX_DATA_DIR}`)
	}
	const binlog_path = process.env.SPHINX_DATA_DIR + '/binlog'
	if (! (await checkExistance(binlog_path))) {
		await fs.mkdir(binlog_path)
	}
}

export async function checkDaemon(onlyCheck=false) {
	var pid = false, results = [];
	try {
		pid = await fs.readFile(`${process.env.SPHINX_BIN_DIR}/${process.env.SEARCHD_PID_FILE}`, 'utf8')
	}
	catch(e) {
		log.warn(`PID-файл searchd не найден`)
	}
	if (pid) {
		if (isNaN(pid)) {
			log.fatal(`${process.env.SEARCHD_PID_FILE} содержит нечисловые данные`)
		}
		pid = +pid
		results = await findProcess('pid', pid)
	}
	if (results.length) {
		const status = results[0]
		if (status.name.indexOf('searchd') !== 0) {
			log.fatal(`ID процесса searchd (PID ${pid}) занят другим процессом (${status.name})`)
		}
		if (! ~status.cmd.indexOf('1chan.generated.conf')) {
			log.fatal(`searchd запущен с неверной конфигурацией (в команде запуска не обнаружено ссылки на «1chan.generated.conf»)`)
		}
		log.succ(`searchd запущен (PID=${pid})`)
		return true
	}
	else {
		const other = await findProcess('name', 'searchd', true)
		if (other.length) {
			log.fatal(`searchd запущен с [PID = ${other.map(p => p.pid).join(', ')}]${pid ? `, что не соответствует указанному в PID-файле (${pid})` :''}`)
		}
		else {
			if (!onlyCheck) {
				return await startDaemon()
			}
			else {
				return false
			}
		}
	}
}

async function startDaemon() {
	if (process.env.SEARCHD_START == 'true') {
		// Indexer must run first before starting searchd
		let indexesExist = true
		for (let i of ['posts', 'forceometer']) {
			if (! (await checkExistance(`${process.env.SPHINX_DATA_DIR}/${i}.sph`))) {
				indexesExist = false
				break
			}
		}
		if (! indexesExist) {
			await runIndexer(true)
		}
		runShell(`${process.env.SPHINX_BIN_DIR}/searchd --config ${process.cwd()}/1chan.generated.conf`)
		await sleep(1000)
		while (! ( await checkDaemon(true) ) ) {
			await sleep(1000)
		}
		return true
	}
	else {
		log.fatal(`searchd не запущен, втозапуск searchd отключен в .env-файле`)
	}
}

export async function runIndexer(silent=false) {
	const exitCode = await runShell(`${process.env.SPHINX_BIN_DIR}/indexer --config ${process.cwd()}/1chan.generated.conf --all --rotate`)
	/*	The exit codes are as follows:
	0, everything went ok
	1, there was a problem while indexing (and if --rotate was specified, it was skipped)
	2, indexing went ok, but --rotate attempt failed	*/
	if (exitCode == 2) {
		log.timed.warn('indexing went ok, but --rotate attempt failed')
	}
	else if (exitCode == 1) {
		log.timed.err('there was a problem while indexing (and if --rotate was specified, it was skipped)', true)
	}
	else {
		log.timed.info(`База данных успешно проиндексирована`)
	}
}

function runShell(script) {
	return new Promise(resolve => {
		const args = script.split(' ')
		const command = args.shift()
		const proc = cp.spawn(command, args)
		proc.on('exit', code => resolve(code))
	})
}

async function checkExistance(path) {
	try {
		await fs.access(path)
		return true
	}
	catch(e) {
		return false
	}
}