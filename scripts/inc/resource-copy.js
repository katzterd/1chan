import log from '#inc/logger.js'
import { smilies_dir } from '#inc/smilies.js'
import { promises as fs } from 'fs'
import ProgressBar from 'progress'
import sleep from '#inc/sleep.js'
import { glob } from 'glob'

const smilies_res_dir = '../resources/smilies'

export async function installSmilies() {
	try {
		const directoryExists = await fs.access(smilies_dir)
		log.succ('Директория смайликов существует')
	}
	catch(e) {
		log.warn('Директории смайликов не существует. Будет скопирован дефолтный набор.')
		await copyToNewDir(smilies_res_dir, smilies_dir)
		fs.chown(smilies_dir, 33, 33)
		const smilies_files = await glob(`${smilies_dir}/*.{png,gif,webp}`)
		for await (const file of smilies_files) {
		fs.chown(file, 33, 33)
		}
	}
}

const homeboards_res_dir = '../resources/homeboards'
const homeboards_dir = '../www/ico/homeboards'

export async function installHomeboards() {
	try {
		const directoryExists = await fs.access(homeboards_dir)
		log.succ('Директория иконок принадлежности существует')
	}
	catch(e) {
		log.warn('Директории иконок принадлежности не существует. Будет скопирован дефолтный набор.')
		await copyToNewDir(homeboards_res_dir, homeboards_dir)
		fs.chown(homeboards_dir, 33, 33)
		const homeboards_files = await glob(`${homeboards_dir}/*.png`)
		for await (const file of homeboards_files) {
		fs.chown(file, 33, 33)
		}
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
