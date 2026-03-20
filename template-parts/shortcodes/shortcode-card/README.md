<!-- TOC -->
- [Shortcode Card](#shortcode-card)
  - [To use with posts](#to-use-with-posts)
  - [to use with general content](#to-use-with-general-content)
  - [Both](#both)


# Shortcode Card

Short code populates card of information and image if one is set.

## To use with posts 

```html
[card post_type="post"][/card]
```

**post_type** argument has to be present in order to render latest post.

Nothing will render if you pass in invalid post type or if you have no posts under given type ( when array returns 0 records ) 

You are able to pass in count of post you would like to retrieve by setting **post_count** attribute

```html
[card post_type="post" post_count="10"][/card]
```

**post_count** defaults to one if argument is not provided

  *Content between [card] and [/card] is ignored if you are calling posts*


  ## to use with general content

content between square brackets ([]) is used to render card

```html
[card]Content to render[/card]
```

## Both

Both options accept image to be passed in. For posts this is a fallback to featured image. Argument is used only if there is no featured image set.
For card with pslain content image is used as image with content as there is no fallback available.

Failing to provide this argument will:

On post cart - if no featured image is set - not to render image part at all, if one is set - using it
On content card - render card without image

Usage:

```html
[card post_type="post" post_count="10" card_img="absolute_path_to_img"][/card]
[card card_img="absolute_path_to_img"]Content to render[/card]
```
