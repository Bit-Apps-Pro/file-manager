module.exports = {
  env: {
    browser: true,
    es6: true,
    commonjs: true
  },
  globals: {
    Atomics: 'readonly',
    SharedArrayBuffer: 'readonly'
  },
  parser: '@typescript-eslint/parser',
  parserOptions: {
    project: './tsconfig.json'
    // sourceType: 'module',
    // requireConfigFile: false,
    // ecmaFeatures: {
    //   jsx: true
    // },
    // ecmaVersion: 8,
    // sourceType: 'module',
    // settings: {
    //   'import/parsers': {
    //     '@typescript-eslint/parser': ['.ts', '.tsx','.js','.jsx']
    //   },
    //   'import/resolver': {
    //     typescript: {},
    //     node: {
    //       extensions: ['.js', '.jsx', '.ts', '.tsx']
    //     }
    //   }
    // }
  },
  extends: [
    'eslint:recommended',
    'plugin:@typescript-eslint/recommended',
    'plugin:prettier/recommended',
    'plugin:react/recommended',
    'plugin:react-hooks/recommended',
    'plugin:import/errors',
    'plugin:import/recommended',
    'plugin:react/jsx-runtime',
    'plugin:jsx-a11y/recommended',
    'airbnb',
    'prettier',
    'airbnb-typescript',
    'plugin:import/typescript',
    'plugin:json/recommended',
    'plugin:storybook/recommended',
    'plugin:cypress/recommended',
    'prettier'
  ],
  plugins: [
    '@typescript-eslint',
    'react',
    'react-hooks',
    'jsx-a11y',
    'import',
    'promise',
    'cypress',
    'prettier'
  ],
  ignorePatterns: ['vite.config.ts', 'commitlint.config.js'],
  rules: {
    indent: 'off',
    allowImplicit: 0,
    semi: ['error', 'never'],
    camelcase: 'error',
    'react/require-default-props': [0, { functions: 'ignore' }],
    'template-curly-spacing': 'off',
    'react/jsx-filename-extension': [1, { extensions: ['.js', '.jsx', '.ts', '.tsx'] }],
    'react/destructuring-assignment': 0,
    'arrow-parens': 0,
    'react/prop-types': 0,
    'max-len': ['error', { code: 350 }],
    'linebreak-style': ['error', 'unix'],
    'react-hooks/exhaustive-deps': 'warn',
    'react/jsx-uses-react': 'off',
    'react/react-in-jsx-scope': 'off',
    'object-curly-newline': [
      'error',
      {
        ImportDeclaration: { consistent: true },
        ExportDeclaration: { consistent: true },
        ObjectPattern: { consistent: true },
        ObjectExpression: { consistent: true }
      }
    ],
    'array-callback-return': 'off',
    'consistent-return': 'off',
    'newline-per-chained-call': ['error', { ignoreChainWithDepth: 4 }],
    'import/no-extraneous-dependencies': [
      'error',
      { devDependencies: ['**/*.test.tsx', '**/*.test.ts'] }
    ],
    'import/no-duplicates': 'error',
    'import/no-self-import': 'error',
    'import/no-relative-packages': 'error',
    'import/no-relative-parent-imports': 'error',
    'import/consistent-type-specifier-style': ['error', 'prefer-inline'],
    '@typescript-eslint/consistent-type-imports': 'error',
    'import/no-empty-named-blocks': 'error',
    'import/no-extraneous-dependencies': 'error',
    'import/no-import-module-exports': 'error',
    'import/newline-after-import': 'error',
    'import/no-useless-path-segments': ['error', { noUselessIndex: true }],
    '@typescript-eslint/no-non-null-assertion': 'error',
    '@typescript-eslint/no-unused-vars': 'error',
    '@typescript-eslint/indent': 'off',
    '@typescript-eslint/semi': 'off',
    'prettier/prettier': [
      'warn',
      {
        // "importOrder": [
        //   "^(^react$|@react|react)",
        //   "<THIRD_PARTY_MODULES>",
        //   "^@/(.*)$",
        //   "^[./]"
        // ],
        // "importOrderSeparation": true,
        // "importOrderSortSpecifiers": true,
        // ...require('./.prettierrc'),
      }
    ],
    '@typescript-eslint/indent': 'off',
    '@typescript-eslint/no-use-before-define': ['error', { functions: false, classes: false }],
    'react/no-unknown-property': ['error', { ignore: ['css'] }],
    'no-param-reassign': [
      'error',
      { props: true, ignorePropertyModificationsForRegex: ['(d|D)raft', 'this', '$'] }
    ]
    // 'react/jsx-first-prop-new-line': [2, 'multiline'],
    // 'react/jsx-max-props-per-line': [1, { maximum: 1, when: 'multiline' }],
    // // 'react/jsx-indent-props': [0, 0],
    // 'react/jsx-closing-bracket-location': [2, 'tag-aligned'],
    // 'implicit-arrow-linebreak': [0, 'beside'],
  }
}
