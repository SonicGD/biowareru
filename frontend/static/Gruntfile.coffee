module.exports = (grunt) ->
  'use strict'
  require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);
  _ = require('underscore')

  jsLibs = [
    'jquery/dist/jquery.min.js',
    'owl.carousel/dist/owl.carousel.min.js',
    'jquery-mousewheel/jquery.mousewheel.min.js',
    'nanogallery/dist/jquery.nanogallery.min.js',
    'social-likes/social-likes.min.js'
  ].map((path) ->
    return '../web/js/' + path;
  );

  skinList = ['default', 'dark', 'help', 'pony', 'soft']
  skinConcatConfigCssFiles = {}

  skinStylusConfig =
    options:
      compress: false
      paths: ['blocks/']
      import: [
        'config.styl',
        'mixins/i-mixins__clearfix.styl'
      ]

  skinStylusItemConfig =
    expand: true
    cwd: 'blocks/'
    src: [
      '**/*.styl',
      '!mixins/**/*.styl',
      '!config*.styl',
    ]
    dest: 'blocks'
    ext: '.css'

  for skinName in skinList
    do (skinName) ->
      skinStylusConfig[skinName] = _.extend({}, skinStylusItemConfig, {
        options: {
          import: ['config.styl', 'mixins/**/*.styl', 'config_' + skinName + '.styl']
        }, ext: '_' + skinName + '.css'
      })
      skinConcatConfigCssFiles['../web/css/style_' + skinName + '.css'] = [
        '../web/js/owl.carousel/dist/assets/owl.carousel.min.css',
        '../web/js/nanogallery/dist/css/nanogallery.min.css',
        '../web/js/nanogallery/dist/css/nanogallery.woff.min.css',
        '../web/js/social-likes/social-likes_flat.css',
        'blocks/**/*_' + skinName + '.css',
        'assets/**/*.css',

      ]


  @initConfig

    connect:
      server:
        options:
          port: 8000,
          base: '../web'
          middleware: (connect, options) ->
            return [
# Serve static files.
              connect.static(options.base)
# Show only html files and folders.
              connect.directory(options.base, {
                hidden: false, icons: true, filter: (file) ->
                  return /\.html/.test(file) || !/\./.test(file);
              })
            ]


    copy:
      images:
        files: [{
          expand: true
          flatten: true
          cwd: 'blocks',
          src: ['**/*.{png,jpg,jpeg,gif,svg}', '!**/*_sprite.{png,jpg,jpeg,gif}']
          dest: '../web/img'
        }]



    clean:
      pubimages:
        src: [
          "../web/img/*.{png,gif,jpg,jpeg,svg}"
        ]


    imagemin:
      options:
        optimizationLevel: 5
      dist:
        files: [
          {
            expand: true
            cwd: '../web/img/'
            src: '**/*.{png,jpg,jpeg}'
            dest: '../web/img/'
          },
          {
            expand: true
            cwd: '../web'
            src: '*.{png,jpg,jpeg,ico}'
            dest: '../web'
          },
        ]

    concat:
      js:
        src: [
          jsLibs.concat('blocks/**/*.js'),
          jsLibs.concat('assets/**/*.js')
        ]
        dest: '../web/js/script.js'

      css:
        files:
        skinConcatConfigCssFiles

    uglify:
      dist:
        files:
          '../web/js/script.min.js': ['<%= concat.js.dest %>']


    jshint:
      files: [
        'blocks/**/*.js'
      ]
      options:
        curly: true
        eqeqeq: true
        eqnull: true
# quotmark: true
        undef: true
        unused: false

        browser: true
        jquery: true
        globals:
          console: true


    watch:
      options:
        livereload: false
        spawn: false

      stylus:
        options:
          livereload: true
        files: [
          'blocks/**/*.styl'
        ]
        tasks: ['stylus', 'newer:concat:css', 'newer:autoprefixer']

      js:
        options:
          livereload: true
        files: [
          '../web/js/**/*.js',
          'blocks/**/*.js'
        ]
        tasks: ['newer:concat:js']

      grunt:
        options:
          livereload: true
        files: [
          'Gruntfile.coffee'  # auto reload gruntfile config
        ]
        tasks: ['newer:concat:js', 'stylus', 'newer:concat:css', 'newer:autoprefixer']

      twig:
        options:
          livereload: true
        files: ['*.twig', 'tmpl/**/*.twig']
        tasks: ['twigRender']

      data:
        options:
          livereload: true
        files: ['data/**/*.json']
        tasks: ['twigRender']

      images:
        files: [
          'blocks/**/*.{png,jpg,jpeg,gif,svg}'
        ]
        tasks: ['copy:images']


    stylus:
    skinStylusConfig


    autoprefixer:
      no_dest:
        options:
          browsers: ['> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1', 'ie 9']       #default
        expand: true
        cwd: '../web/css/'
        src: '*.css'
        dest: '../web/css/'


    cssmin: {
      my_target: {
        files: [{
          expand: true,
          cwd: '../web/css/',
          src: ['*.css'],
          dest: '../web/css/min/',
          ext: '.css'
        }]
      }
    }

    open:
      mainpage:
        path: 'http://localhost:8000/p-index.html'

    twigRender:
      your_target: {
        files: [
          {
            data: 'data/index.json',
            expand: true,
            cwd: './tmpl',
            src: ['p-*.twig'],
            dest: '../web/',
            ext: '.html'
          }
        ]
      },


  @registerTask('default', ['concat:js', 'stylus', 'newer:concat:css', 'autoprefixer', 'twigRender'])
  @registerTask('livereload', ['default', 'connect', 'open', 'watch'])
  @registerTask('publish', ['default', 'uglify', 'cssmin'])
