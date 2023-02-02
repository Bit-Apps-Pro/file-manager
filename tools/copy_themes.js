#!/usr/bin/env node


const fs = require('fs')
const path = require('path')

const pwd = fs.realpathSync(`${__dirname}/..`)
const themesDir = `${pwd}/assets/themes`

const themes = JSON.parse(fs.readFileSync(`${pwd}/tools/themes.json`).toString())

const reCreateDir = () => {
    if (fs.existsSync(themesDir)) {
        fs.rmSync(themesDir, { recursive: true, force: true })
    }
    fs.mkdirSync(themesDir)
}

const copyFolderRecursiveSync = ( source, target ) => {
    let files = [];

    const targetFolder = path.join( target, path.basename( source ) );
    if ( !fs.existsSync( targetFolder ) ) {
        fs.mkdirSync( targetFolder );
    }

    // Copy
    if ( fs.lstatSync( source ).isDirectory() ) {
        files = fs.readdirSync( source );
        files.forEach( function ( file ) {
            const curSource = path.join( source, file );
            console.log('curSource', curSource, fs.lstatSync( curSource ).isDirectory())
            console.log("\n")
            if ( fs.lstatSync( curSource ).isDirectory() ) {
                copyFolderRecursiveSync( curSource, targetFolder );
            } else {
                fs.copyFileSync( curSource, path.join(targetFolder, file));
            }
        } );
    }
}

const copyThemeVariant = (theme, variant, data) => {
    const themeDir = `${themesDir}/${theme}/${variant}`
    fs.mkdirSync(themeDir)
    fs.copyFileSync(data.path, `${themeDir}/${variant}.min.css`)
    fs.writeFileSync(
        `${themeDir}/${variant}.json`,
        JSON.stringify({
            "name": data?.name,
            "cssurls": `./${variant}.min.css`,
            "author": data?.author,
            "email": data?.email,
            "license": data?.license,
            "link": data?.link,
            "image": data?.image,
            "description": data?.description
        })
    )
}

const copyThemeAsset = (theme, resource) => {
    const themeDir = `${themesDir}/${theme}`
    copyFolderRecursiveSync(resource, `${themeDir}/`)
}

const main = () => {
    reCreateDir()
    Object.keys(themes).forEach(theme => {
        fs.mkdirSync(`${themesDir}/${theme}`)
        Object.keys(themes[theme]['variants']).forEach(variant => {
            copyThemeVariant(theme, variant, themes[theme]['variants'][variant])
        })
        themes[theme]['resources'].forEach(resource => {
            copyThemeAsset(theme, resource)
        })

    })
}

main()