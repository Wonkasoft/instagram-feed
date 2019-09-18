let gulp = require('gulp'),
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
local = json.read('./package.json').get('localhost'),
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
		proxy: local + siteName,
		https: true,
		port: 4000
	});
});

gulp.task('sass', function () {

	return gulp.src( './sass/style.scss' )

	.pipe(sourcemaps.init())

	.pipe(plumber(plumberErrorHandler))

	.pipe(sass())

	.pipe(cleanCSS())

	.pipe(concat('wonkasoft-instafeed-public.css'))

	.pipe(sourcemaps.write('./maps'))

	.pipe(gulp.dest('./public/css'))

	.pipe(browserSync.stream());

});

gulp.task('sass-admin', function () {

	return gulp.src('./sass/admin-style.scss')

	.pipe(sourcemaps.init())

	.pipe(plumber(plumberErrorHandler))

	.pipe(sass())

	.pipe(cleanCSS())

	.pipe(concat('wonkasoft-instafeed-admin.css'))

	.pipe(sourcemaps.write('./maps'))

	.pipe(gulp.dest('./admin/css'))

	.pipe(browserSync.stream());

});



gulp.task('watch', function() {

	gulp.watch('**/sass/*.scss', gulp.series(gulp.parallel('sass', 'sass-admin'))).on('change', browserSync.reload);
	gulp.watch('**/*.php').on('change', browserSync.reload);

});

gulp.task('default', gulp.series(gulp.parallel('sass', 'sass-admin', 'watch', 'browser-sync')));