import log from '#inc/logger.js'
import { checkDB, dumpSQL } from '#inc/database.js'
import chalk from 'chalk'
import prompts from 'prompts'
import { listAdmins, createAdmin } from '#inc/admin.js'

console.log(chalk.bold.hex('#fd5b00').inverse(`1chan installation script v.${process.env.npm_package_version}`))

const dbOK = await checkDB()
if (!dbOK) {
	const response = await prompts({
		type: 'multiselect',
		name: 'dump',
		message: `Загрузить дамп базы данных? ${chalk.red.bold('(Будут удалены все данные!)')}`,
		choices: [{title: "Да", value: true}, {title: "Нет", value: false}]
	})
	if (response.dump?.[0]) {
		await dumpSQL()
	}
}
else {
	log.succ('Все таблицы на месте')
}

console.log('\n')


const admins = (await listAdmins()).filter(a => a.class=="0")
if (admins) {
	log.info('Найдены учетные записи админов:')
	console.log(admins.map(a => {
		a.key = a.key.replace(/./g, '*')
		return a
	}))
}
else {
	log.info('Учетных записей	админов не найдено')
	const q = await prompts({
		type: 'confirm',
		name: 'create',
		message: 'Создать учетную запись администратора?'
	})
	if (q.create) {
		const {user, pass} = await prompts([{
			type: 'text',
			name: 'user',
			message: 'Логин:',
			validate: val => val.match(/[^\s|]{3,50}/)
		}, {
			type: 'invisible',
			name: 'pass',
			message: 'Пароль:',
			validate: val => val.match(/[^\s|]{3,50}/)
		}])
		const created = await createAdmin(user, pass)
		if (created == 'OK') {
			log.succ(`Учётная запись «${user}» создана`)
		}
	}
}

process.exit(1)