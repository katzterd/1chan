import log from '#inc/logger.js'
import { checkDB, dumpSQL } from '#inc/database.js'
import chalk from 'chalk'
import { listAdmins, createAdmin } from '#inc/admin.js'
import { installSmilies, installHomeboards } from '#inc/resource-copy.js'
import checkEnv from '#inc/check-env.js'

checkEnv(["ADMIN_LOGIN", "ADMIN_PASSWD"])

const admcreds = {
	user: process.env.ADMIN_LOGIN,
	pass: process.env.ADMIN_PASSWD
}


console.log(chalk.bold.hex('#fd5b00').inverse(`1chan installation script v.${process.env.npm_package_version} \n`))

const dbOK = await checkDB()
if (!dbOK) {
		await dumpSQL()
}
else {
	log.succ('Все таблицы на месте')
}

console.log('\n')

const admins = (await listAdmins())?.filter(a => a.class=="0")
if (admins) {
	log.info('Найдена учетная запись администратора:')
	console.log(admins.map(a => {
		a.key = a.key.replace(/./g, '*')
		return a
	}))
}
else {
	log.info('Учетной записи администратора не найдено')
		const created = await createAdmin(admcreds.user, admcreds.pass)
		if (created == 'OK') {
			log.succ(`Учётная запись «${admcreds.user}» создана`)
		}
}

console.log('\n')

await installSmilies()
await installHomeboards()

console.log('\n')

log.succ(`✔️ Завершение скрипта установки`)
process.exit(0)
