import typescript from './gulp/rollup-plugin-ts';

export default {
    entry: './ts/app/main.ts',

    plugins: [
        typescript()
    ]
}