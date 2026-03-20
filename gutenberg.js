// https://www.npmjs.com/package/node-cmd
/**
 * Generate boilerplate for gutenberg block
 *  node gutenberg block-name
 */

const fs = require('fs');
const cmd = require('node-cmd');
const name = process.argv.pop();
const fieldName = name.split('-').join('_');
const initFunc = fieldName + '_' + new Date().getTime() + '_init';
const abbr = name.split('-').map(line => line.charAt(0).toLocaleLowerCase()).join('');

const blockTitle = name
  .split('-')
  .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
  .join(' ');

  // console.log(name);
  // console.log(blockTitle);
  // console.log(fieldName);
  // console.log(abbr);

/**
 * name
 * getRandomId
 * blockTitle
 * fieldName
 *
 */

// const data = {
//   name,
//   id: getRandomId(),
//   blockTitle,
//   fieldName,
// };
// console.log(data);

const bgImgId = getRandomId(); // this is for background image. conditional logic requires ids to be the same
const items = [
  {
    title: 'snippet-markup.php',
    content: `
    <?php
    $content = get_field('content');
    $ctas = get_field('buttons') ? get_field('buttons')['links'] ?: [] : [];
  
    // Classes
    $blockClasses = ['km-${name}'];
    $contentClasses = ['km-${abbr}-content', 'km-wysiwyg'];
  
    // Colours
    $backgroundColour = get_field('bg_colour')['picker']['slug'];
    $contentColour = get_field('content_colour')['picker']['slug'];
    $linkColour = get_field('link_colour')['picker']['slug'];
    $bulletColour = get_field('bullet_colour')['picker']['slug'];
  
    if($backgroundColour) array_push($blockClasses, 'has-' . $backgroundColour . '-background-colour');
    if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');
    if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');
    if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour . '-bullet-colour');

    // Options
    $contentLocation = get_field('content_location');
    if($contentLocation) array_push($blockClasses, 'km-${abbr}-content-' . $contentLocation);

    ?>
  
  
  <section class="<?php echo implode(' ', $blockClasses); ?>">
  
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

    <div class="km-${abbr}-wrap">
  
      <?php // CONTENT ?>
      <?php if($content) : ?>
        <div class="<?php echo implode(' ', $contentClasses); ?>">
        <div>
          <?php echo $content; ?>
        </div>
      </div>
      <?php endif; ?>
  
      <?php // CTAS ?>
      <?php if(!empty($ctas)) : ?>
        <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
          'links' => $ctas,
          'classPrefix' => 'km-${abbr}', // optional
          'extraButtonClass' => 'cta-hover-x' // optional
        ] );
        ?>   
      <?php endif; ?>
        
    </div>
  </section> 

    `,
  },
//   {
//     title: 'README.md',
//     content: `
// # Gutenberg block: ${blockTitle}`,
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

    .#{$block}.#{$abbr}-content-center {
      text-align:center;
    }
  
    /* Content
    ==========*/
    .#{$block} .#{$abbr}-content {
      font-weight: 400;
    }
    
    @media (min-width: 981px) {
      .#{$block} {
        padding: 150px 40px;
      }
    }
    
    @media (max-width: 980px) {
      .#{$block} {
        padding: 70px 40px;
      }
    }
    
    @media (max-width: 640px) {
      .#{$block} {
        padding: 50px 30px;
      }
    }
  
    `,
  },
  {
    title: `acf-${name}.json`,
    content: `
    [
      {
          "key": "group_${getRandomId()}",
          "title": "Block: ${blockTitle}",
          "fields": [
              {
                  "key": "field_${getRandomId()}",
                  "label": "${blockTitle}",
                  "name": "",
                  "type": "accordion",
                  "instructions": "",
                  "required": 0,
                  "conditional_logic": 0,
                  "wrapper": {
                      "width": "",
                      "class": "",
                      "id": ""
                  },
                  "open": 0,
                  "multi_expand": 0,
                  "endpoint": 0
              },
              {
                  "key": "field_${getRandomId()}",
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
                  "tabs": "all",
                  "toolbar": "full",
                  "media_upload": 1,
                  "delay": 0
              },
              {
                  "key": "field_${getRandomId()}",
                  "label": "Buttons",
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
                  "name": "buttons",
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
                      "group_5f05876a1211c"
                  ],
                  "display": "group",
                  "layout": "block",
                  "prefix_label": 0,
                  "prefix_name": 1
              },
              {
                  "key": "field_${getRandomId()}",
                  "label": "Colours",
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
                  "label": "Background",
                  "name": "bg_colour",
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
                      "field_5ecf6860e03bb"
                  ],
                  "display": "group",
                  "layout": "block",
                  "prefix_label": 0,
                  "prefix_name": 1
              },
              {
                  "key": "field_${getRandomId()}",
                  "label": "Content",
                  "name": "content_colour",
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
                      "field_5ecf6860e03bb"
                  ],
                  "display": "group",
                  "layout": "block",
                  "prefix_label": 0,
                  "prefix_name": 1
              },
              {
                  "key": "field_${getRandomId()}",
                  "label": "Links",
                  "name": "link_colour",
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
                      "field_5ecf6860e03bb"
                  ],
                  "display": "group",
                  "layout": "block",
                  "prefix_label": 0,
                  "prefix_name": 1
              },
              {
                  "key": "field_${getRandomId()}",
                  "label": "Bullets",
                  "name": "bullet_colour",
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
                      "field_5ecf6860e03bb"
                  ],
                  "display": "group",
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
                "label": "Content location",
                "name": "content_location",
                "type": "select",
                "instructions": "",
                "required": 0,
                "conditional_logic": 0,
                "wrapper": {
                    "width": "",
                    "class": "",
                    "id": ""
                },
                "choices": {
                    "left": "Left",
                    "center": "Center"
                },
                "default_value": "left",
                "allow_null": 0,
                "multiple": 0,
                "ui": 1,
                "ajax": 0,
                "return_format": "value",
                "placeholder": ""
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
              "placement": "top",
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
          "menu_order": 2,
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
  {
    title:'module.js',
    content: `
/*
* 
* module.js - JS has modular scope
* common.js - JS has to be within km-bundle
*
*/

  function ${initFunc}(){
    console.log('${blockTitle} loaded');
  }
  ${initFunc}();
    `
  }
];

const gutenbergBlockContent = `
<?php
acf_register_block(array(
  'name' => '${name}', // this forms directory name. In this example = "gutenberg-${name}"
  'title' => __('${blockTitle}'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block',
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

      // Write ACF
      fs.writeFile(
        `template-parts/gutenberg-${name}/gutenberg/gutenberg-acf.php`,
        gutenbergBlockContent,
        (err) => {
          if (err) throw err;
          console.log(`gutenberg-acf.php content saved.`);
        }
      );

      // WRITE FILES
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

    } else {
      console.log('error', err);
    }
  }
);







function getRandomId() {
  const min = Math.ceil(0);
  const max = Math.floor(1000);
  const time = new Date().getTime();
  const val = Math.floor(Math.random() * (max - min + 1)) + min;
  return (
    time + '_' + val + '_' + Math.floor(Math.random() * (max - min + 1)) + min
  );
}
