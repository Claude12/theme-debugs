// https://www.npmjs.com/package/node-cmd
/**
 * Generate setup directoies for gutenberg block
 */

const fs = require('fs');
const cmd = require('node-cmd');
const name = process.argv.pop();
const fieldName = name.split('-').join('_');
const blockTitle = name
  .split('-')
  .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
  .join(' ');
const abbr = name.split('-').map(line => line.charAt(0).toLocaleLowerCase()).join('');
const initFunc = fieldName + '_' + new Date().getTime() + '_init';
const bgImgId = getRandomId(); // this is for background image. conditional logic requires ids to be the same
const items = [
  {
    title: 'module.js',
    content: `
function ${initFunc}() {
  // Props set here will override anything set inside data atrributes of element.
  const props = {
    cap: 1,
    slidesPerViewBreakpoint: 1200, // this breakpoint will be added to swiper breakpoints with WP admin value slides per view
    enableMedia: '(min-width: 100px)',
    swiperProps: {
      spaceBetween: 0,
      breakpoints: {
        320: {
          slidesPerView: 1,
        },
      },
    },
  };

  initSwiper('.km-${name}', props);
}
${initFunc}();
    `,
  },
  {
    title: 'single-slide.php',
    content: ` 
<?php 
  $item = $template_args['item']; // All data from repeater element is here
?>
<?php if($item) var_dump($item); ?>
    `,
  },
  {
    title: 'snippet-markup.php',
    content: `
<?php
$source = get_field('${fieldName}') ?: []; // ACF group name
$slides = $source['slides']; // name of repeater block in ACF
$swiperConfig = get_field('swiper_config') ?: [];

$filePath = '/template-parts/gutenberg-${name}/';

// remove slides that are marked as unvisible
foreach($slides as $k => $val){
  if(!$val['visible']) unset($slides[$k]);
}

if(!$slides) return; 

$setup = [
  'hook-above' => $filePath . 'hook-above', //optional - inject html within wrapper
  //'hook-above-args' => [], // optional. any arguments to hook above
  //'section-extra-classes' => ['class-one', 'class-two'], // optional
  'section-title' => '${name}', // optional. will be replaced with km-block- uniqueid() if not presented ( will create class name on main block )
  'data' => $slides, // defaults to empty array. content of repeater block;
  'slide-markup-path' => $filePath . 'single-slide', // required
  //'hook-below' => $filePath . 'hook-below' // //optional - inject html within wrapper,
  //'hook-below-args' => [], // optional. any arguments to hook below
];

?>

<?php //Create swiper instance with setup  ?>
<?php hm_get_template_part( get_template_directory() . '/template-parts/common/swiper/snippet-markup.php', [
    'setup' => $setup,
    'swiper-config' => $swiperConfig 
  ] );
?>   
    `,
  },
  {
title: 'hook-above.php',
content:`
<?php // Module background Image ?>
<?php
  get_template_part('template-parts/common/module-background-image/module-background-image', null, [
    'image' => get_field('image'),
    'image_props' => get_field('image_props'),
  ]);
?>

<?php // Module Overlay ?>
<?php
  get_template_part('template-parts/common/module-overlay/module-overlay', null, [
    'overlay' => get_field('overlay')
  ]);
?>
`,
  },
//   {
//     title: 'README.md',
//     content: `
// # Gutenberg block: ${blockTitle}
// This code works, but it seems that the whole system with swiper is a bit "noisy" I would suggest going back to basics - using swiper API itself every time. Simpler code - easier to understand and far more flexible.
// `,
//   },
  {
    title: 'module.scss',
    content: `
/*
* 
* module.scss - CSS has modular scope
* common.scss - CSS has to be within km-bundle
* critical.scss - CSS should be injected within head of the document ( Stuff above the fold )
* 
*/


/* ${blockTitle}
================*/

$bg: #e8eff7;
$colour: #707070;

$block: km-${name};
$abbr: km-${abbr};

.#{$block} {
  position: relative;
  box-sizing: border-box;
  background: $bg;
  color: $colour;
}

.#{$block} .#{$abbr}-wrap {
  position: relative;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  z-index: 2;
}

@media (min-width: 981px) {
  .#{$block} .swiper-container {
    min-height: 400px;
  }
}

    `,
  },
  {
    title: `acf-${name}.json`,
    content: `
[
  {
      "key": "group_${getRandomId()}_acf",
      "title": "Block: ${blockTitle}",
      "fields": [
          {
              "key": "field_${getRandomId()}_acf",
              "label": "Content",
              "name": "",
              "type": "tab",
              "instructions": "",
              "required": 0,
              "conditional_logic": 0,
              "wrapper": {
                  "width": "",
                  "class": "",
                  "id": ""
              },
              "placement": "left",
              "endpoint": 0
          },
          {
              "key": "field_${getRandomId()}_acf",
              "label": "",
              "name": "${fieldName}",
              "type": "group",
              "instructions": "",
              "required": 0,
              "conditional_logic": 0,
              "wrapper": {
                  "width": "",
                  "class": "",
                  "id": ""
              },
              "layout": "block",
              "sub_fields": [
                  {
                      "key": "field_${getRandomId()}",
                      "label": "Slides",
                      "name": "slides",
                      "type": "repeater",
                      "instructions": "",
                      "required": 0,
                      "conditional_logic": 0,
                      "wrapper": {
                          "width": "",
                          "class": "",
                          "id": ""
                      },
                      "collapsed": "",
                      "min": 0,
                      "max": 0,
                      "layout": "row",
                      "button_label": "Add slide",
                      "sub_fields": [
                          {
                              "key": "field_${getRandomId()}",
                              "label": "Visible",
                              "name": "visible",
                              "type": "true_false",
                              "instructions": "",
                              "required": 0,
                              "conditional_logic": 0,
                              "wrapper": {
                                  "width": "",
                                  "class": "",
                                  "id": ""
                              },
                              "message": "",
                              "default_value": 1,
                              "ui": 1,
                              "ui_on_text": "",
                              "ui_off_text": ""
                          },
                          {
                              "key": "field_${getRandomId()}",
                              "label": "Content",
                              "name": "content",
                              "type": "wysiwyg",
                              "instructions": "",
                              "required": 0,
                              "conditional_logic": 0,
                              "wrapper": {
                                  "width": "",
                                  "class": "",
                                  "id": ""
                              },
                              "default_value": "",
                              "placeholder": "",
                              "prepend": "",
                              "append": "",
                              "maxlength": ""
                          }
                      ]
                  }
              ]
          },
          {
            "key": "field_${getRandomId()}",
            "label": "Background Image",
            "name": "",
            "type": "tab",
            "instructions": "",
            "required": 0,
            "conditional_logic": 0,
            "wrapper": {
                "width": "",
                "class": "",
                "id": ""
            },
            "placement": "left",
            "endpoint": 0
        },
        {
            "key": "field_${bgImgId}",
            "label": "Image",
            "name": "image",
            "type": "image",
            "instructions": "",
            "required": 0,
            "conditional_logic": 0,
            "wrapper": {
                "width": "",
                "class": "",
                "id": ""
            },
            "return_format": "url",
            "preview_size": "thumbnail",
            "library": "all",
            "min_width": "",
            "min_height": "",
            "min_size": "",
            "max_width": "",
            "max_height": "",
            "max_size": "",
            "mime_types": ""
        },
        {
            "key": "field_${getRandomId()}",
            "label": "",
            "name": "image_props",
            "type": "clone",
            "instructions": "",
            "required": 0,
            "conditional_logic": [
                [
                    {
                        "field": "field_${bgImgId}",
                        "operator": "!=empty"
                    }
                ]
            ],
            "wrapper": {
                "width": "",
                "class": "",
                "id": ""
            },
            "clone": [
                "group_5f7d9d6f01d0b"
            ],
            "display": "group",
            "layout": "block",
            "prefix_label": 0,
            "prefix_name": 1
        },
        {
            "key": "field_${getRandomId()}",
            "label": "Overlay",
            "name": "overlay",
            "type": "clone",
            "instructions": "",
            "required": 0,
            "conditional_logic": 0,
            "wrapper": {
                "width": "",
                "class": "",
                "id": ""
            },
            "clone": [
                "group_5ff77cdac6539"
            ],
            "display": "seamless",
            "layout": "block",
            "prefix_label": 0,
            "prefix_name": 1
        },
          {
              "key": "field_${getRandomId()}",
              "label": "Options",
              "name": "",
              "type": "tab",
              "instructions": "",
              "required": 0,
              "conditional_logic": 0,
              "wrapper": {
                  "width": "",
                  "class": "",
                  "id": ""
              },
              "placement": "left",
              "endpoint": 0
          },
          {
              "key": "field_${getRandomId()}",
              "label": "",
              "name": "swiper_config",
              "type": "clone",
              "instructions": "",
              "required": 0,
              "conditional_logic": 0,
              "wrapper": {
                  "width": "",
                  "class": "",
                  "id": ""
              },
              "clone": [
                  "group_5e99b713aeddb"
              ],
              "display": "seamless",
              "layout": "block",
              "prefix_label": 0,
              "prefix_name": 0
          }
      ],
      "location": [
          [
              {
                  "param": "block",
                  "operator": "==",
                  "value": "acf\/${name}"
              }
          ]
      ],
      "menu_order": 0,
      "position": "normal",
      "style": "default",
      "label_placement": "top",
      "instruction_placement": "label",
      "hide_on_screen": "",
      "active": true,
      "description": ""
  }
]    
    `,
  },
];

const gutenbergBlockContent = `
<?php
acf_register_block(array(
  'name' => '${name}', // this forms directory name. In this example = "gutenberg-heading"
  'title' => __('${blockTitle}'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( '${name}', get_template_directory_uri() . '/assets/prod/gutenberg-${name}/module.css' );
    wp_enqueue_script( '${name}', get_template_directory_uri() . '/assets/prod/gutenberg-${name}/module.js', ['km-core-scripts','km-bundle-scripts'], '', true );
  },
));
`;

if (fs.existsSync(`template-parts/gutenberg-${name}`)) {
  throw Error(`Gutenberg block: "${name}" already exists`);
}

cmd.get(
  `
      mkdir template-parts/gutenberg-${name}
      mkdir template-parts/gutenberg-${name}/gutenberg
      touch template-parts/gutenberg-${name}/gutenberg/gutenberg-acf.php
  `,
  function (err, data, stderr) {
    if (!err) {
      fs.writeFile(
        `template-parts/gutenberg-${name}/gutenberg/gutenberg-acf.php`,
        gutenbergBlockContent,
        (err) => {
          if (err) throw err;
          console.log(`gutenberg-acf.php content saved.`);
        }
      );
    } else {
      console.log('error', err);
    }
  }
);

items.forEach(function (file) {
  let item = `template-parts/gutenberg-${name}/${file.title}`;

  cmd.get(`touch ${item}`, function (err, data, stderr) {
    if (!err) {
      fs.writeFile(item, file.content, (err) => {
        if (err) throw err;
        console.log(`${item} content saved.`);
      });
    } else {
      console.log('error', err);
    }
  });
});

function getRandomId() {
  const min = Math.ceil(0);
  const max = Math.floor(1000);
  const time = new Date().getTime();
  const val = Math.floor(Math.random() * (max - min + 1)) + min;
  return (
    time + '_' + val + '_' + Math.floor(Math.random() * (max - min + 1)) + min
  );
}
