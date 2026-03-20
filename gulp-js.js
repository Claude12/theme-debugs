const gulp = require('gulp');
const babel = require('gulp-babel');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const data = require('./gulp-theme-config.json');
const polyfills = data.polyfills.map(
  (p) => `./assets/js/polyfills/${p}.{js,min.js}`
);
const plugins = data.ketchupPlugins.map(
  (p) => `./assets/js/plugins/${p}.{js,min.js}`
);
const helpers = data.globalFuncs.map(
  (p) => `./assets/js/helpers/${p}.{js,min.js}`
);
const compileSet = [...polyfills, ...plugins, ...helpers];
const libs = data.libraries.map(
  (l) => `./assets/js/libraries/${l}/*.{js,min.js}`
);
const themeJs = [
  './template-parts/**/common.{js,min.js}',
  './assets/js/theme/*.{js,min.js}',
  './template-parts/**/gutenberg/*.{js,min.js}',
];

const moduleJsSource = ['./template-parts/**/module.{js,min.js}'];

function compileJS() {
  return gulp
    .src(compileSet, { allowEmpty: true })
    .pipe(concat('./core.js'))
    .pipe(babel({ presets: ['@babel/preset-env'], comments: false }))
    .pipe(gulp.dest('./assets/js/tmp'));
}

function concatLibs() {
  return gulp
    .src(libs, { allowEmpty: true })
    .pipe(concat('./libs.js'))
    .pipe(gulp.dest('./assets/js/tmp'));
}

function bundleCoreJs() {
  return gulp
    .src('./assets/js/tmp/*.{js,min.js}', { allowEmpty: true })
    .pipe(concat('./km-core.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./assets/js/'));
}

function bundleThemeJs() {
  return gulp
    .src(themeJs, { allowEmpty: true })
    .pipe(concat('./km-bundle.min.js'))
    .pipe(babel({ presets: ['@babel/preset-env'], comments: false }))
    .pipe(uglify())
    .pipe(gulp.dest('./assets/js'));
}

function buildModuleJs() {
  return gulp
    .src(moduleJsSource, { allowEmpty: true })
    .pipe(babel({ presets: ['@babel/preset-env'], comments: false }))
    .pipe(uglify())
    .pipe(gulp.dest('./assets/prod'));
}

module.exports = {
  compileSet,
  libs,
  themeJs,
  moduleJsSource,
  compileJS,
  concatLibs,
  bundleCoreJs,
  bundleThemeJs,
  buildModuleJs
};
