const gulp = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const uglifycss = require('gulp-uglifycss');
const gap = require('gulp-append-prepend');

const coreStylesSource = ['./assets/styles/core/*.{css,scss,min.css}'];
const themeStylesSource = ['./template-parts/**/common.{css,scss,min.css}', './assets/styles/theme/*.{css,scss,min.css}','./assets/styles/theme/_*.{css,scss,min.css}'];
const moduleStylesSource = ['./template-parts/**/module.scss','./template-parts/**/_*.scss'];
const criticalStylesSource = ['./template-parts/**/critical.scss'];
const moduleIconSource = ['./template-parts/**/icons.svg'];

const weddingPlannerSource = ['./custom/*.scss'];

function weddingPlannerStyles() {
  return (
    gulp
      .src(weddingPlannerSource)
      .pipe(sourcemaps.init())
      .pipe(sass().on('error', sass.logError))
      .pipe(autoprefixer())
      .pipe(concat('km-wedding-planner.min.css'))
      .pipe(sourcemaps.write('.'))
      .pipe(
        uglifycss({
          uglyComments: true,
        })
      )
      .pipe(gulp.dest('./custom/'))
  );
}



function coreStyles() {
  return (
    gulp
      .src(coreStylesSource)
      .pipe(sourcemaps.init())
      .pipe(sass().on('error', sass.logError))
      .pipe(autoprefixer())
      .pipe(concat('km-core.min.css'))
      .pipe(sourcemaps.write('.'))
      .pipe(
        uglifycss({
          uglyComments: true,
        })
      )
      .pipe(gulp.dest('./assets/styles/'))
  );
}

function themeStyles() {
  return (
    gulp
      .src(themeStylesSource)
      .pipe(sourcemaps.init())
      .pipe(sass().on('error', sass.logError))
      .pipe(autoprefixer())
      .pipe(concat('km-bundle.min.css'))
      .pipe(sourcemaps.write('.'))
      .pipe(
        uglifycss({
          uglyComments: true,
        })
      )
      .pipe(gulp.dest('./assets/styles/'))
  );
}

function moduleStyles() {
  return (
    gulp
      .src(moduleStylesSource)
      .pipe(sourcemaps.init())
      .pipe(sass().on('error', sass.logError))
      .pipe(autoprefixer())
      .pipe(sourcemaps.write('.'))
      .pipe(
        uglifycss({
          uglyComments: true,
        })
      )
       .pipe(gulp.dest('./assets/prod'))
  );
}

function moduleIcons() {
  return (
    gulp
      .src(moduleIconSource,{ allowEmpty: true })
      .pipe(gulp.dest('./assets/prod'))
  );
}


function criticalStyles() {
  return (
    gulp
      .src(criticalStylesSource)
      .pipe(sass().on('error', sass.logError))
      .pipe(autoprefixer())
      .pipe(concat('critical-css.php'))
      .pipe(
        uglifycss({
          uglyComments: true,
        })
      )
      .pipe(
        gap.prependText('<style id="km-critical-css">')
      )
      .pipe(gap.appendText('</style>'))
       .pipe(gulp.dest('./assets/prod'))
  );
}

module.exports = {
  coreStylesSource,
  themeStylesSource,
  moduleStylesSource,
  criticalStylesSource,
  moduleIconSource,
  weddingPlannerSource,
  coreStyles,
  themeStyles,
  moduleStyles,
  criticalStyles,
  moduleIcons,
  weddingPlannerStyles
};
