import chalk from 'chalk'

console.log(chalk.bold.hex('#fd5b00').inverse(`1chan service script v.${process.env.npm_package_version}`))