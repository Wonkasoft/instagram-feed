var gulp = require('gulp'),
sass = require('gulp-sass'),
sourcemaps = require('gulp-sourcemaps'),
concat = require('gulp-concat'),
path = require('path'),
cleanCSS = require('gulp-clean-css'),
plumber = require('gulp-plumber'),
notify = require('gulp-notify'),
browserSync = require('browser-sync').create(),
json = require('json-file'),
themeName = json.read('./package.json').get('name'),
siteName = json.read('./package.json').get('siteName'),
themeDir = '../' + themeName,
plumberErrorHandler = { errorHandler: notify.onError({

	title: 'Gulp',

	message: 'Error: <%= error.message %>',

	line: 'Line: <%= line %>'

})

};
sass.compiler = require('node-sass');

// Static server
gulp.task('browser-sync', function() {
	browserSync.init({
		proxy: 'https://localhost/' + siteName,
		port: 4000
	});
});

gulp.task('sass', function () {

	return gulp.src('./scss/style.scss')

	.pipe(sourcemaps.init())

	.pipe(plumber(plumberErrorHandler))

	.pipe(sass())

	.pipe(cleanCSS())

	.pipe(concat('style.css'))

	.pipe(sourcemaps.write('./maps'))

	.pipe(gulp.dest('./assets/css/'))

	.pipe(browserSync.stream());

});

gulp.task('sass-admin', function () {

	return gulp.src('./scss/admin-style.scss')

	.pipe(sourcemaps.init())

	.pipe(plumber(plumberErrorHandler))

	.pipe(sass())

	.pipe(cleanCSS())

	.pipe(concat('admin.css'))

	.pipe(sourcemaps.write('./maps'))

	.pipe(gulp.dest('./assets/css/'))

	.pipe(browserSync.stream());

});



gulp.task('watch', function() {

	gulp.watch('**/*.scss', gulp.series(gulp.parallel('sass', 'sass-admin'))).on('change', browserSync.reload);
	gulp.watch('**/*.php', gulp.series(gulp.parallel('sass', 'sass-admin'))).on('change', browserSync.reload);

});

gulp.task('default', gulp.series(gulp.parallel('sass', 'sass-admin', 'watch', 'browser-sync')));