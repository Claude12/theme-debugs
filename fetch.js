/**
 *
 * Fetches branches from github repo
 *  gutenberg block : node fetch module-name
 *  snippet block (2.km_snippets) : node fetch module-name snippet
 *  if this fails ensure you have folder called git-imports in km_unicorn
 */

const fs = require('fs');
const cmd = require('node-cmd');
const snippet = process.argv[2];

let repo = 'KM_Gutenberg_blocks.git';
if (process.argv[3] && process.argv[3] === 'snippet')
  repo = '2.km_snippets.git';

const copydir = require('copy-dir');
const rimraf = require('rimraf');

cmd.get(
  `
      cd git-imports
      git clone --single-branch --branch ${snippet} git@github.com:Ketchup-Marketing/${repo}
      cd ..
  `,
  function (err, data, stderr) {
    fs.readdir('./git-imports', (err, files) => {
      let src = './git-imports/' + files[0];

      fs.readdir(src, (err, files) => {
        files.forEach((file) => {
          if (!file.includes('.git') && !file.includes('.md')) {
            copydir(
              `${src}/${file}`,
              `./template-parts/${file}`,
              {
                utimes: true, // keep add time and modify time
                mode: true, // keep file mode
                cover: true, // cover file when exists, default is true
              },
              function (err) {
                if (err) {
                  throw err;
                } else {
                  console.log(`"${file}" imported successfully`);
                  rimraf(src, function () {
                    console.log(`Task completed. Clearing "${src}"`);
                  });
                }
              }
            );
          }
        });
      });
    });
  }
);
