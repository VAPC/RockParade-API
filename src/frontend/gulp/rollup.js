var gulp = require('gulp');
var rollup = require('rollup').rollup;
var typescript = require('rollup-plugin-typescript');

gulp.task('rollup', function () {
    return rollup({
        entry: 'ts/main.ts',
        plugins: [
            typescript()
        ]
    }).then(function (bundle) {
        return bundle.write({
            format: 'iife',
            dest: '../../web/app.js'
        });
    });
});