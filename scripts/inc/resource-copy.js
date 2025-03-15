import log from '#inc/logger.js'
import { smilies_dir } from '#inc/smilies.js'
import { promises as fs } from 'fs'
import ProgressBar from 'progress'
import sleep from '#inc/sleep.js'
import { glob } from 'glob'

const smilies_res_dir = '../resources/smilies'

export async function installSmilies() {

    const smilies_files = await glob(`${smilies_dir}/*.{png,gif}`)

	if (smilies_files.length === 0) {
		log.warn('Смайлики не существуют. Будет скопирован дефолтный набор.')
		await copyToNewDir(smilies_res_dir, smilies_dir)
		fs.chown(smilies_dir, 33, 33)
		for await (const f of smilies_files) {
		fs.chown(f, 33, 33)
		}
	} 
	else { 
	log.succ('Смайлики существуют') 
	}
	
}

const homeboards_res_dir = '../resources/homeboards'
const homeboards_dir = '../www/ico/homeboards'

export async function installHomeboards() {

    const homeboards_files = await glob(`${homeboards_dir}/*.png`)

	if (homeboards_files.length === 0) {
		log.warn('Иконки принадлежности не существуют. Будет скопирован дефолтный набор.')
		await copyToNewDir(homeboards_res_dir, homeboards_dir)
		fs.chown(homeboards_dir, 33, 33)
		for await (const f of homeboards_files) {
		fs.chown(f, 33, 33)
		}
	} 
	else { 
	log.succ('Иконки принадлежности существуют') 
	}
	
}

async function copyToNewDir(src, dest) {
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
