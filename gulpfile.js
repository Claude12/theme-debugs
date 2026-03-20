const gulp = require('gulp');
const del = require('del');
const deleteLines = require('gulp-delete-lines');
const concat = require('gulp-concat');
const gap = require('gulp-append-prepend');

const {
  coreStyles,
  coreStylesSource,
  moduleStylesSource,
  themeStyles,
  moduleStyles,
  themeStylesSource,
  criticalStylesSource,
  weddingPlannerSource,
  criticalStyles,
  moduleIconSource,
  moduleIcons,
  weddingPlannerStyles
} = require('./gulp-styles');

const {
  compileJS,
  concatLibs,
  bundleCoreJs,
  bundleThemeJs,
  buildModuleJs,
  compileSet,
  themeJs,
  libs,
  moduleJsSource
} = require('./gulp-js');

const initCoreBundle = [...compileSet, ...libs];

function populateShortCodes() {
  return gulp
    .src('./template-parts/shortcodes/**/shortcode.php', { allowEmpty: true })
    .pipe(concat('./theme-shortcodes.php'))
    .pipe(gulp.dest('./'));
}

function populateIcons() {
  return gulp
    .src(
      [
        './assets/svg-icons/theme-wide.php',
        './template-parts/**/svg-symbols.php',
      ],
      { allowEmpty: true }
    )
    .pipe(concat('svg-set.php'))
    .pipe(gulp.dest('./assets/svg-icons'));
}

function cleanGutenbergAcf() {
  return gulp
    .src(
      [
        './assets/gutenberg-acf/independent.php',
        './template-parts/**/gutenberg/gutenberg-acf.php',
      ],
      { allowEmpty: true }
    )
    .pipe(
      deleteLines({
        filters: [/(?<![\w\d])\<\?php(?![\w\d])/],
      })
    )
    .pipe(concat('gacf-bundle.php'))
    .pipe(gulp.dest('./assets/gutenberg-acf/'));
}

function populateGutenbergBlocks() {
  return gulp
    .src(['./assets/gutenberg-acf/gacf-bundle.php'], { allowEmpty: true })
    .pipe(
      gap.prependText(`
    <?php
      function my_acf_init() {
        `)
    )
    .pipe(gap.appendText(`}`))
    .pipe(gulp.dest('./assets/gutenberg-acf/'));
}

function watchGulp() {
  gulp.watch(initCoreBundle, gulp.series(compileJS, concatLibs, bundleCoreJs));
  gulp.watch(themeJs, gulp.series(bundleThemeJs));
  gulp.watch(moduleJsSource, gulp.series(buildModuleJs));
  gulp.watch([...moduleStylesSource,'./template-parts/**/_*.scss'],gulp.series(moduleStyles));
  gulp.watch(moduleIconSource,gulp.series(moduleIcons));
  gulp.watch([...criticalStylesSource,'./template-parts/**/_*.scss'],gulp.series(criticalStyles));
  gulp.watch(coreStylesSource, gulp.series(coreStyles));
  gulp.watch(themeStylesSource, gulp.series(themeStyles));
  gulp.watch(weddingPlannerSource, gulp.series(weddingPlannerStyles));
  gulp.watch('./template-parts/shortcodes/', gulp.series(populateShortCodes));
  gulp.watch(
    ['./assets/svg-icons/theme-wide.php', './template-parts/**/svg-symbols.php'],
    gulp.series(populateIcons)
  );
  gulp.watch(
    ['./gutenberg-acf/independant.php', './template-parts/**/gutenberg/**'],
    gulp.series(cleanGutenbergAcf, populateGutenbergBlocks)
  );
  return;
}

// Watch all files
exports.default = watchGulp;

// Run initially
exports.build = gulp.series(
  coreStyles,
  themeStyles,
  moduleStyles,
  criticalStyles,
  compileJS,
  concatLibs,
  bundleCoreJs,
  buildModuleJs,
  bundleThemeJs,
  populateShortCodes,
  populateIcons,
  cleanGutenbergAcf,
  populateGutenbergBlocks,
  moduleIcons,
  weddingPlannerStyles
);
