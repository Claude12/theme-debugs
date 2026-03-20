<!-- vscode-markdown-toc -->

- 1. [Intro](#Intro)
- 2. [Swiper](#Swiper)
     - 2.1. [Files](#Files)
     - 2.1.1. [\_arrows.scss](#_arrows.scss)
     - 2.1.2. [\_bullets.scss](#_bullets.scss)
     - 2.1.3. [\_arrows.scss](#_arrows.scss-1)
     - 2.1.4. [\_next-btn.php](#_next-btn.php)
     - 2.1.5. [\_next-btn.php](#_next-btn.php-1)
     - 2.1.6. [app.js](#app.js)
     - 2.1.7. [snippet-markup.php](#snippet-markup.php)
     - 2.1.8. [style.scss](#style.scss)
     - 2.1.9. [svg-symbols.php](#svg-symbols.php)
- 3. [Detailed explanation about:](#Detailedexplanationabout:)
     - 3.1. [app.js](#app.js-1)
     - 3.1.1. [Properties](#Properties)
     - 3.1.2. [Id's](#Ids)
     - 3.1.3. [propsFromAttributes](#propsFromAttributes)
     - 3.2. [snippet-markup.php](#snippet-markup.php-1)
     - 3.2.1. [Hooks](#Hooks)
- 4. [ACF](#ACF)
- 5. [Creating Module (Detailed)](#CreatingModuleDetailed)
     - 5.1. [Main module directory](#Mainmoduledirectory)
     - 5.2. [Register ACF gutenberg block](#RegisterACFgutenbergblock)
     - 5.3. [Create fields and link to block](#Createfieldsandlinktoblock)
     - 5.4. [Create main entry point](#Createmainentrypoint)
     - 5.4.1. [\$setup array](#setuparray)
     - 5.5. [Set up Javascript side.](#SetupJavascriptside.)
     - 5.5.1. [window.acf](#window.acf)
     - 5.5.2. [init function](#initfunction)
     - 5.5.3. [slidesPerViewBreakpoint](#slidesPerViewBreakpoint)
     - 5.6. [Set up single slide markup](#Setupsingleslidemarkup)
- 6. [Quick Guide](#QuickGuide)
     - 6.1. [Populate Gutenberg Block](#PopulateGutenbergBlock)
     - 6.2. [Populate Gutenberg Block without swiper](#PopulateGutenbergBlockwithoutswiper)

<!-- vscode-markdown-toc-config
	numbering=true
	autoSave=true
	/vscode-markdown-toc-config -->
<!-- /vscode-markdown-toc --># Re usable Swiper block

Documentation for re usable swiper block and the way we use it.

## 1. <a name='Intro'></a>Intro

Swiper block has to have certain minimum mark-up for this to work. It is considered a good practice not to change this as it could lead to errors.

Minimum markup :

```html
<div class="swiper-container">
  <div class="swiper-wrapper">
    <div class="swiper-slide"></div>
    <!-- ..more slides .. -->
  </div>
  <div class="swiper-pagination"></div>
</div>
```

We have extended minimum markup to make it easier to format:

```html
<div class="block">
  <div class="block-inner-wrapper">
    <div class="swiper-container">
      <div class="swiper-wrapper">
        <div class="swiper-slide"></div>
        <!-- ..more slides .. -->
      </div>
      <div class="swiper-pagination"></div>
    </div>
    <div></div>
  </div>
</div>
```

We can see that there is quite a bit of code without even getting into actual module markup. This is why all this logic is now stored centrally.

## 2. <a name='Swiper'></a>Swiper

Swiper directory is located in **template-parts** -> **common** -> **swiper**.
This directory contains all centrally stored code needed to run all every swiper carousel.

### 2.1. <a name='Files'></a>Files

Description of files within directory

#### 2.1.1. <a name='_arrows.scss'></a>\_arrows.scss

SCSS file containing styling for side arrows.

#### 2.1.2. <a name='_bullets.scss'></a>\_bullets.scss

SCSS file containing styling for bullets (circle) navigation.

#### 2.1.3. <a name='_arrows.scss-1'></a>\_arrows.scss

SCSS file containing styling for fraction navigation (alternative to bullets)

#### 2.1.4. <a name='_next-btn.php'></a>\_next-btn.php

HTML markup needed for next button. This is extracted into separate file because it is used in multiple places.
Code used to include:

```php
<?php  if($nav && $sideArrows) include(plugin_dir_path( dirname( __FILE__ ) ) . 'swiper/_next-btn.php'); ?>
```

#### 2.1.5. <a name='_next-btn.php-1'></a>\_next-btn.php

Same as above with previous button.

#### 2.1.6. <a name='app.js'></a>app.js

This file contains main javascript for calling KetchupSwiper. Think of this as middleware before initialising swiper instance.

#### 2.1.7. <a name='snippet-markup.php'></a>snippet-markup.php

Contains main HTML markup needed for swiper to work. This file should not be amended unless we extend functionality of swiper. There are several variables to pass into this file to ensure file being left intact. These are described later in this section.

#### 2.1.8. <a name='style.scss'></a>style.scss

Master styles file for swiper.

#### 2.1.9. <a name='svg-symbols.php'></a>svg-symbols.php

File that contains SVG's used within swiper.

## 3. <a name='Detailedexplanationabout:'></a>Detailed explanation about:

### 3.1. <a name='app.js-1'></a>app.js

**initSwiper** function accepts two arguments. First is for section selector which this particular swiper represents and **config** objects that contains all options for this particular instance.

Typical function call ( this would happen in different file ) :

```js
const props = {
	cap: 7,
	slidesPerViewBreakpoint: 1200,
	enableMedia: '(min-width: 100px)',
	swiperProps: {
		spaceBetween: 0,
		breakpoints: {},
	},
};
initSwiper('.km-logo-slider', props);
}
```

From example above : Target all instances with class name of **.km-logo-slider** and initiate swiper instance with given properties.

#### 3.1.1. <a name='Properties'></a>Properties

Swiper instance properties are cascading in 2 levels ( 2 levels including core swiper )

1. Defaults set in app.js
2. SwiperProps are merged with data attributes found on swiper instance.

**Properties defined as `data-` will overwrite those in default settings as well as props passed into function**

#### 3.1.2. <a name='Ids'></a>Id's

app.js takes care of adding id to each section. Id is populated from abbreviation of main block.

For example :

main block is called **km-my-block**, then id would be **km-mb-count(INT)**. Prefix **km-** is global and therefore is skipped within abbreviation. Every modules parent wrap class names should start with this prefix to minimise issues within admin area.

#### 3.1.3. <a name='propsFromAttributes'></a>propsFromAttributes

This is a function responsible for `gathering` data attributes from main block to populate user choices in regards to swiper configuration. Simple switch statement is used to populate object that is later merged with existing properties set elsewhere. Be vigilant when adding new props - some properties require 2 level objects and more.

### 3.2. <a name='snippet-markup.php-1'></a>snippet-markup.php

This core files takes care of main logic for each swiper block. This is where you will pass in data related to your module such as swiper configuration and slide data.

Main components within this file:

1. Swiper wrapper markup
2. HTML hook (optional)
3. Side arrow (Prev)
4. Swiper core markup
5. Side arrow (Next)
6. Swiper navigation
7. HTML hook (optional)

#### 3.2.1. <a name='Hooks'></a>Hooks

Logic in snippet-markup.php allows injecting extra content within this block using hooks. Hooks are includes specified within actual module.

## 4. <a name='ACF'></a>ACF

Swiper configuration provides set of re usable user options that should be passed to snippet-markup.php

Clone option should be used to link swiper configuration. Each module can have different collection of configuration if needed. Typically, however, whole config file would be cloned.

This is an example of how swiper config will be called at implementation stage :

```php
$swiperConfig = get_field('swiper_config') ?: [];
```

## 5. <a name='CreatingModuleDetailed'></a>Creating Module (Detailed)

There are several required steps to be taken when you create new module. Some steps described here will not related (or be required) for swiper. They are included for completeness.

### 5.1. <a name='Mainmoduledirectory'></a>Main module directory

Create main directory for the module prefixed with **gutenberg-** in you template-directory. Second part of directory name should represent your module. For example if you module is logo slider, you would name your folder - **gutenberg-logo-slider**

### 5.2. <a name='RegisterACFgutenbergblock'></a>Register ACF gutenberg block

New block should be created ( registered ) before it appears in gutenberg menu.

Create directory (within main module dir) called **gutenberg** and file within that called **gutenberg-acf.php**

Typical file content:

```php
<?php

acf_register_block(array(
'name' => 'logo-slider',
'title' => __('Logo Slider'),
'description' => __('Enables Logo carousel with option links.'),
'render_callback' => 'ketchup_gutenberg_block',
'category' => 'ketchup-modules',
'icon' => array(
	'background' => '#bd1218',
	'foreground' => '#fff',
	'src' => 'tickets-alt',
),
'keywords' => array( 'heading', 'block heading' ),
));
```

Code above is from ACF and can be found in their documentation. Our worry in this instance is `name` and `render_callback`. All other keys are self explanatory. Category is ketchup-modules and this will appear in WP admin if there is at least one block available. This process is automated.

**name** - has to be the same as **Main module directory** content after **gutenberg**. So if your director y is gutenberg-logo-slider, then name will be logo-slider. This is necessary for **render_callback**. This function is a way to automate directory rendering. Using correct name ensure correct mark up is passed to the block.

Function : ( this is just to show how it looks and should not be used. )

```php
function ketchup_gutenberg_block( $block ) {

$slug = str_replace('acf/', '', $block['name']);

$target = get_template_directory() . "/template-parts/gutenberg-{$slug}/snippet-markup.php";

if(file_exists( $target )) include( $target );

}

```

**<?php** on top of **gutenberg-acf.php** is optional and is automatically stripped out. This is left in to help with legibility.

### 5.3. <a name='Createfieldsandlinktoblock'></a>Create fields and link to block

Create necessary fields in WP admin ACF and display them if block is equal to your block name!

### 5.4. <a name='Createmainentrypoint'></a>Create main entry point

Main entry point is file called **snippet-markup.php**. This is where you link it up with main swiper file. Create this file in main module directory.

Sample content of this file:

```php
<?php

$source = get_field('logo_slider') ?: []; // ACF group name
$slides = $source['slides']; // name of repeater block in ACF
$swiperConfig = get_field('swiper_config') ?: [];
$filePath = '/template-parts/gutenberg-logo-slider/';

// remove slides that are marked as unvisible
foreach($slides as $k => $val){
	if(!$val['visible']) unset($slides[$k]);
}

if(!$slides) return;

$setup = [
	'hook-above' => $filePath . 'hook-above.php',
	'section-id' => 'my-unique-id',
	'section-extra-classes' => ['class-one', 'class-two'],
	'section-title' => 'logo-slider',
	'data' => $slides,
	'slide-markup-path' => $filePath . 'single-slide.php',
	'hook-below' => $filePath . 'hook-below.php'
];

?>

<?php hm_get_template_part( get_template_directory() . '/template-parts/common/swiper/snippet-markup.php', [
'setup' => $setup,
'swiper-config' => $swiperConfig
] );
?>
```

Above is typical markup for this file. Think of it as middleware where you setup all your fields. Way of retrieving variables is fully up to developer and is not opinionated. **\$setup** array and **hm_get_template_part** is.

#### 5.4.1. <a name='setuparray'></a>\$setup array

Setup array contains information ( required and optional ) that is passed to main swiper block

##### hook-above

**Optional** - path to file that should be injected between main swiper wrapper and inner wrapper. Path starts with theme root. For example:

```php
'hook-above' =>'/template-parts/gutenberg-logo-slider/hook-above.php',
```

##### section-id

**Optional** Ability to assign specific id to a block. This should not be used since javascript will override it. It has been added for future improvements.

Example

```php
'section-id' => 'my-unique-id',
```

##### section-extra-classes

**Optional** There is one required class for the module, however, we can pass addition classes as they appear to module. This is expected to be an array.

Example:

```php
'section-extra-classes' => ['class-one', 'class-two'],
```

##### 'section-title'

** Required ** This is main identifier for our block and it is required. This key forms two things - main class and inner wrap class.

main block will get **km-{section-title}** and inner wrap would get **km-{st}** where `st` is abbreviation of section-title.
For example you pass in **logo-slider** - you would get following outcome

```php
<section  class="km-logo-slider">
	<div class="km-ls">
	...
	</div>
</section>
```

Example:

```php
'section-title' => 'logo-slider',
```

##### data

**Required** This key is required and is storing all slides that should appear within our swiper instance.

Note that if you have `visible` toggle on you slides - you should filter them before calling main block. For example:

Assume that our slide has ACF field _visible_ and this is boolean. So if it is false - we don't render our field:

```php
foreach($slides as $k => $val){
	if(!$val['visible']) unset($slides[$k]);
}
```

Example:

```php
'data' => $slides,
```

##### 'slide-markup-path'

**Required** This key holds path (theme root level) to markup that is used to render single swiper slide. Consider following markup

```php
<div class="swiper-container">
	<div class="swiper-wrapper">
		<div class="swiper-slide">
			<div class="my-markup">
			</div>
		</div>
	</div>
</div>
```

file set in this path would contain markup for **my-markup.php** div only.

Example:

```php
'slide-markup-path' => '/template-parts/gutenberg-logo-slider/single-slide.php',
```

##### hook-below

**Optional** - path to file that should be injected between main swiper wrapper and inner wrapper. Path starts with theme root. For example:

```php
'hook-below' =>'/template-parts/gutenberg-logo-slider/hook-below.php',
```

##### Call master swiper template

When this is all set all we need is to fetch swiper configuration and call swiper master

Example:

```php
$swiperConfig = get_field('swiper_config') ?: [];

<?php hm_get_template_part( get_template_directory() . '/template-parts/common/swiper/snippet-markup.php', [
	'setup' => $setup,
	'swiper-config' => $swiperConfig
] );
?>
```

**swiper_config** in `get_field` does depend on your setup in WP.

Side note: `Nothing will be rendered if **\$slides** count is 0;

### 5.5. <a name='SetupJavascriptside.'></a>Set up Javascript side.

Create file **app.js** in Main module directory. ( Name of js file is not important as everything is bundled together ).

#### 5.5.1. <a name='window.acf'></a>window.acf

This is a way to determine whether you are on admin area or client side because this object is available only in admin area.

This means that based on this assumption - we can create following check:

```js
if (window.acf) {
	window.acf.addAction('render_block_preview', init);
	window.acf.addAction('remount', init);
} else {
	init();
}

function init(){...}
```

Why is this needed? Adding action with ACF ensures that HTML element is rendered in view before you call you main JS logic. Think of this as DOM content ready for ACF.

This will run **init** function on block add and **remount**. This means that it is a good idea to provide clean up code in your code to ensure minimal instance count.

#### 5.5.2. <a name='initfunction'></a>init function

This is a function that is run on remount and add block in admin area and on DOM ready on client side. For swiper we just set up props and run main swiper function

```js
function init() {

// Props set here will override anything set inside data atrributes of element.

const props = {
	cap: 7,
	slidesPerViewBreakpoint: 1200,
	enableMedia: '(min-width: 100px)',
	swiperProps: {},
};

initSwiper('.km-logo-slider', props);
}


```

#### 5.5.3. <a name='slidesPerViewBreakpoint'></a>slidesPerViewBreakpoint

Swiper configuration provides an option to set how many slides are visible on large screens. This means that we need to add breakpoint when this setting kicks in. **slidesPerViewBreakpoint** does exactly that. Another breakpoint is added into swiper props with this width and value set from ACF swiper config.

if set to 1200 :

```js
breakpoints: {
	1200: {
		slidesPerView: Integer set in admin area WP,
	},
}
```

### 5.6. <a name='Setupsingleslidemarkup'></a>Set up single slide markup

Create file called ** single-slide.php** in main module directory. This is going to be used within a loop. and all array items set in **\$data** will be render using this file.

Single item data is available in **$item = $template_args['item'];** . Item variable now contains all fields set in repeater.

Anything you put in this file will be render within single **swiper-slide** in main module.

## 6. <a name='QuickGuide'></a>Quick Guide

NOTE: Both examples assume you use swiper config provided (template-parts/common/swiper/acf-swiper-config.json). if this is not true - you will need to alter cloned group id manually from WP admin.

All gutenberg blocks should be named with hyphens as spaces.

### 6.1. <a name='PopulateGutenbergBlock'></a>Populate Gutenberg Block

Assuming you are in site root.

1.  `cd wp-content/themes/km_unicorn`
2.  `node gutenberg-swiper {module-name}`
3.  Upload newly created directory to server
4.  Import json in WP admin ACF (template-parts/{module-name}/acf-{module-name}.json)
5.  Your block is now available in gutenberg blocks

### 6.2. <a name='PopulateGutenbergBlockwithoutswiper'></a>Populate Gutenberg Block without swiper

Assuming you are in site root.

1.  `cd wp-content/themes/km_unicorn`
2.  `node gutenberg {module-name}`
3.  Upload newly created directory to server
4.  Import json in WP admin ACF (template-parts/{module-name}/acf-{module-name}.json)
5.  Your block is now available in gutenberg blocks
