import chalk from 'chalk'

const methods = {
	succ: {
		method: 'log',
		style: chalk.green
	},
	warn: {
		method: 'warn',
		style: chalk.yellow
	},
	info: {
		method: 'info',
		style: chalk.blue
	},
	err: {
		method: 'error',
		style: chalk.red
	},
	fatal: {
		method: 'error',
		style: chalk.red.bold,
		extra: () => process.exit(1)
	},
}

const e = { timed: {} }

Object.keys(methods).forEach(msgType => {
	e[msgType] = str => {
		console[methods[msgType].method](methods[msgType].style(str))
		if (methods[msgType]?.extra) methods[msgType].extra()
	}
	e.timed[msgType] = str => {
		console[methods[msgType].method](logTime() +' '+ methods[msgType].style(str))
		if (methods[msgType]?.extra) methods[msgType].extra()
	}
})

function logTime() {
	return chalk.gray(`[${new Date().toLocaleString()}]`)
}

export default e