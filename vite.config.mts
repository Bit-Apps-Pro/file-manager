/// <reference types="vite/client" />
import commonjs from '@rollup/plugin-commonjs'
import react from '@vitejs/plugin-react'
import path from 'node:path'
import fs from 'node:fs'
import { AddressInfo } from 'node:net'
import detectPort from 'detect-port'
import csso from 'postcss-csso'
import { Alias, Plugin, defineConfig, normalizePath } from 'vite'

import * as tsconfig from './tsconfig.json'

// const nextId = incstr.idGenerator()
let chunkCount = 0
function hash() {
  return Math.round(Math.random() * (999 - 1) + 1)
}

function readAliasFromTsConfig(): Alias[] {
  // eslint-disable-next-line prefer-regex-literals
  const pathReplaceRegex = new RegExp(/\/\*$/, '')
  return Object.entries(tsconfig.compilerOptions.paths).reduce((aliases, [fromPaths, toPaths]) => {
    const find = fromPaths.replace(pathReplaceRegex, '')
    // @ts-ignore
    const toPath = toPaths[0].replace(pathReplaceRegex, '')
    const replacement = path.resolve(__dirname, toPath)
    aliases.push({ find, replacement })
    return aliases
  }, [] as Alias[])
}

// @ts-ignore
export default defineConfig(({ mode }) => {
  // const isProd = mode === 'production'
  const folderName = path.basename(process.cwd())
  console.log(normalizePath(path.resolve(__dirname, 'frontend/src')))
  return {
    root: 'frontend/src',
    base: mode === 'development' ? `/wp-content/plugins/${folderName}/frontend/src/` : '',
    plugins: [
      react({
        jsxImportSource: '@emotion/react',
        babel: {
          plugins: ['@emotion/babel-plugin']
        },
        jsxRuntime: 'automatic'
        // @ts-ignore
        // fastRefresh: true,
      }),
      commonjs(),
      // babel()
      // TODO: PWA not working
      // for PWA resources genarate icon from this link https://realfavicongenerator.net/ and FULL DOCS https://vite-plugin-pwa.netlify.app/
      // VitePWA({
      //   ...PwaConfig(),
      // }),
      // css: {
      //   preprocessorOptions: {
      //     modules:{

      //     }
      //   }
      // }
      setDevServerConfig()
    ],
    css: {
      postcss: {
        plugins: [csso()]
      }
    },
    resolve: { alias: readAliasFromTsConfig() },

    build: {
      outDir: '../../assets',
      emptyOutDir: true,
      // assetsDir: './',
      rollupOptions: {
        input: path.resolve(__dirname, 'frontend/src/main.tsx'),
        output: {
          entryFileNames: 'main.js',
          manualChunks: {
            'react-vendor': ['react', 'react-dom'],
            '@emotion/react': ['@emotion/react'],
            '@tanstack/react-query': ['@tanstack/react-query'],
            '@tanstack/react-query-devtools': ['@tanstack/react-query-devtools'],
            'react-router-dom': ['react-router-dom'],
            antd: ['antd']
          },
          // compact: true,
          // validate: true,
          // generatedCode: {
          // arrowFunctions: true
          // objectShorthand: true
          // },

          chunkFileNames: () => {
            // console.log(fInfo)
            // if (fInfo.name === 'bit-flow-pro') {
            //   return path.resolve(__dirname, '../bf-pro/[name]-[hash].js')
            // }
            return '[name]-[hash].js'
          },

          assetFileNames: fInfo => {
            const pathArr = fInfo?.name?.split('/')
            const fileName = pathArr?.[pathArr.length - 1]

            // console.log(fInfo.name, fileName)

            if (fileName === 'main.css') {
              return 'main.css'
            }
            if (fileName === 'logo.svg') {
              return 'logo.svg'
            }

            return `bf-${hash()}-${chunkCount++}.[ext]`
          }
        }
      }
    },
    test: {
      // globals: true,
      environment: 'jsdom',
      setupFiles: './config/test.setup.ts'
      // since parsing CSS is slow
      // css: true,
    },
    server: {
      // origin: 'http://localhost:3000',
      cors: true, // required to load scripts from custom host
      strictPort: true, // strict port to match on PHP side
      port: 3000,
      hmr: { host: 'localhost' }
      // commonjsOptions: { transformMixedEsModules: true },
    }
  }
})

function setDevServerConfig(): Plugin {
  return {
    name: 'vite-plugin-set-dev-server-config',
    async config(_, env) {
      if (env?.mode === 'development') {
        let port = getStoredPort()
        if (!port) {
          port = await detectPort(3000).then((detectedPort: number) => detectedPort)
          updateStoredPort(port)
        }
        return { server: { port, origin: `http://localhost:${port}` } }
      }
      removeStoredPort()
    },
    configureServer(server) {
      if (server.httpServer) {
        server.httpServer.once('listening', () => {
          const { port } = server.httpServer?.address() as AddressInfo
          const storedPort = getStoredPort()
          if (port !== storedPort) {
            updateStoredPort(port)
          }
        })

        server.watcher.add([
          'port',
        ])

        server.watcher.on('change', (file: string) => {
          if (file === 'port') {
            server.config.logger.warnOnce('Server restarting for origin mismatch', { timestamp: true })
            server.restart()
          }
        })
      }
    },
  }
}

const portFile = path.resolve(__dirname, './port')

function getStoredPort() {
  let port = 0
  if (fs.existsSync(portFile)) {
    port = Number(fs.readFileSync(portFile))
  }

  return port
}

function updateStoredPort(port: number) {
  fs.writeFileSync(portFile, String(port))
}

function removeStoredPort() {
  if (fs.existsSync(portFile)) {
    fs.rmSync(portFile)
  }
}
