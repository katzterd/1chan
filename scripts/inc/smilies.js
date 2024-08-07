import log from '#inc/logger.js'
import { promises as fs } from 'fs'
import sizeof from 'image-size'
import { cacheSet } from '#inc/kvs.js'

const www = '../www'
const smilies_dir = `${www}/img/smilies`
const img_ext = ['gif', 'png', 'jpg', 'jpeg', 'wepb']

export async function updateSmilies() {
	const files = await fs.readdir(smilies_dir)
	const images = files.filter(f => {
		const ext = f.match(/(.+)\.(.+)/)
		return ext && img_ext.includes(ext[2].toLowerCase())
	})

	const smilies = []

	images.forEach(img => {
		try {
			const props = sizeof(`${smilies_dir}/${img}`)
			smilies.push({
				name: img.match(/(.+)\.(.+)/)[1],
				ext: props.type,
				height: props.height,
				width: props.width
			})
		}	catch (e) {
			log.err(`${img}: Неверный формат файла`)
		}
	})

	await fs.writeFile(`${www}/smilies.json`, JSON.stringify(smilies))
	await cacheSet(`Smilies::list`, smilies)

	log.succ("Список смайликов обновлен")
}

export async function watchSmilies() {
	const ac = new AbortController()
	const { signal } = ac;

	(async () => {
		try {
			const watcher = fs.watch(smilies_dir, { signal });
			for await (const event of watcher) {
				if (event.eventType == 'rename') {
					await updateSmilies()
				}
			}
		} catch (err) {
			if (err.name === 'AbortError')
				return;
			throw err;
		}
	})()
}