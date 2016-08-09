var gulp = require('gulp');
var watch = require('gulp-watch');
var run = require('run-sequence');
var server = require('./lib/server');
var reload = server.reload;


gulp.task('dev', function () {
    run(['script', 'server', 'test:tdd']);
    watch(['ts/**/*.ts'], function () {
        run(['script', 'test:script', 'reload']);
    });
    watch(['test/**/*.ts'], function () {
        run(['test:script']);
    });
});

gulp.task('reload', function() {
    reload();
});

gulp.task('server', function () {
    return server([
        '../../web'
    ], {
        port: 3000
    });
});