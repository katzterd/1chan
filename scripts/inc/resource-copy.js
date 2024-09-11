import log from '#inc/logger.js'
import { smilies_dir } from '#inc/smilies.js'
import { promises as fs } from 'fs'
import ProgressBar from 'progress'
import sleep from '#inc/sleep.js'

const smilies_res_dir = '../resources/smilies'

export async function installSmilies() {
	try {
		const directoryExists = await fs.access(smilies_dir)
	}
	catch(e) {
		log.warn('Директории смайликов не существует. Будет скопирован дефолтный набор.')
		await copyToNewDir(smilies_res_dir, smilies_dir)
	}
}

const homeboards_res_dir = '../resources/homeboards'
const homeboards_dir = '../www/ico/homeboards'

export async function installHomeboards() {
	try {
		const directoryExists = await fs.access(homeboards_dir)
	}
	catch(e) {
		log.warn('Директории иконок принадлежности не существует. Будет скопирован дефолтный набор.')
		await copyToNewDir(homeboards_res_dir, homeboards_dir)
	}
}

async function copyToNewDir(src, dest) {
	await fs.mkdir(dest)
	const files = await fs.readdir(src)

	const bar = new ProgressBar('Копирование файлов [:bar] :percent', {
		complete: '=',
		incomplete: ' ',
		width: 20,
		total: files.length
	})

	for (const f of files) {
		await fs.copyFile(`${src}/${f}`, `${dest}/${f}`)
		bar.tick()
	}
}