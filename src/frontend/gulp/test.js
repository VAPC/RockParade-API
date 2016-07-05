var gulp = require('gulp');
var Server = require('karma').Server;

gulp.task('tdd', function (done) {
    new Server({
        configFile: require('path').resolve('karma.conf.js')
    }, done).start();
});