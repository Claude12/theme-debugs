const gulp = require('gulp');
const fs = require('fs');
const file = require('gulp-file');
const sass = require('gulp-sass');
const { colours } = require('./assets/styles/colours.json');
const bulletData = require('./assets/svg-icons/bullet-icon.json');
const uglifycss = require('gulp-uglifycss');
const generatedBulletPath = './assets/svg-icons/generated-bullet-icons.svg';

fs.readFile('./assets/svg-icons/bullet-icon.svg', 'utf8', function (err, data) {
  let bulletResult = null;

  if (err) {
    return console.log(err);
  }

  bulletResult = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="${bulletData.viewBox}" xml:space="preserve">
  <defs>
  <style>
    g {
      display: none;
    }
    g:target {
      display: inline;
    }
  </style>
</defs>
`;

  colours.forEach((colour) => {
    let currColour = data.replace(
      `<g id="bullet-template">`,
      `<g id="${colour.slug}-bullet-icon" style="fill: ${colour.value};">`
    );
    bulletResult += currColour;
  });

  bulletResult += '</svg>';

  try {
    if (fs.existsSync(generatedBulletPath)) {
      fs.unlinkSync(generatedBulletPath);
    }

   // buildBulletIcons(bulletResult); Not in use currently. Icons are built using php
  } catch (err) {
    console.error(err);
  }
});

const colorFunction = `
@each $value, $label, $slug in $colours {
  .has-#{$slug}-background-colour {
    background-color: $value!important;
  }
  .has-#{$slug}-colour {
    color: $value!important;
  }
  .has-#{$slug}-border-colour {
    border-color: $value!important;
  }
  .has-#{$slug}-fill {
    fill: $value!important;
  }
  .has-#{$slug}-link-colour a {
    color: $value!important;
  }
  .has-#{$slug}-link-colour a:hover {
    color: inherit!important;
  }

  .has-#{$slug}-bullet-colour li::before  {
    content: url('../svg-icons/generated-bullet-icons.svg##{$slug}-bullet-icon')!important;
    color: #{$value}!important;
  }

}
`;

let colourString = '';
let acfString = '';

colours.forEach((colour, index) => {
  let acfOutput = `${colour.value} : ${colour.label} : ${colour.slug}`;
  let output = `(${colour.value}, ${colour.label}, ${colour.slug})`;
  if (index === colours.length - 1) {
    output += ';';
  } else {
    output += ',';
  }
  colourString = colourString + output;

  acfString =
    acfString +
    `${acfOutput}
`;
});

acfString += ``;

const data = `
// Do not modify this file. Add colours in colours.json and class name modification in gulp-colours.js! Restart gulp if colours.json is modified. Gulp is not watching this file.
$colours: ${colourString}
${colorFunction}
`;

function buildBulletIcons(data) {
  return gulp
    .src('./assets/svg-icons/generated-bullet-icons.svg', {
      allowEmpty: true,
    })
    .pipe(file('generated-bullet-icons.svg', data))
    .pipe(gulp.dest('./assets/svg-icons/'));
}

function buildColoursCSS() {
  return gulp
    .src('./assets/styles/theme/colours.scss', { allowEmpty: true })
    .pipe(file('colours.scss', data))
    .pipe(sass().on('error', sass.logError))
    .pipe(
      uglifycss({
        uglyComments: true,
      })
    )
    .pipe(gulp.dest('./assets/styles/'));
}

function buildColoursACF() {
  return gulp
    .src('./assets/styles/colours.php', { allowEmpty: true })
    .pipe(file('colours.php', acfString))
    .pipe(gulp.dest('./assets/styles/'));
}

const coloursSCSS = './assets/styles/theme/colours.scss';
const coloursPHP = './assets/styles/colours.php';

try {
  if (fs.existsSync(coloursSCSS)) {
    fs.unlinkSync(coloursSCSS);
  }
  if (fs.existsSync(coloursPHP)) {
    fs.unlinkSync(coloursPHP);
  }

  buildColoursCSS();
  buildColoursACF();
} catch (err) {
  console.error(err);
}

// module.exports = {
//   buildColoursCSS,
//   buildColoursACF,
// };
