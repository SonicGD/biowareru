module.exports = (grunt) ->
  'use strict'
  require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);
  _ = require('underscore')

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
    cwd:    'blocks/'
    src:    [
      '**/*.styl',
      '!mixins/**/*.styl',
      '!config*.styl',
    ]
    dest: 'blocks'
    ext:  '.css'
  
  for skinName in skinList
    do (skinName) ->
      skinStylusConfig[skinName] = _.extend({}, skinStylusItemConfig, { options: { import: ['config.styl', 'mixins/**/*.styl', 'config_' + skinName + '.styl'] }, ext:  '_' + skinName + '.css' })
      skinConcatConfigCssFiles['publish/style_' + skinName + '.css'] = ['lib/**/*.css', 'blocks/**/*_' + skinName + '.css' ]


  @initConfig

    connect:
      server:
        options:
          port: 8000,
          base: ''
          middleware: (connect, options) ->
            return [
              # Serve static files.
              connect.static(options.base)
              # Show only html files and folders.
              connect.directory(options.base, { hidden:false, icons:true, filter:(file) ->
                return /\.html/.test(file) || !/\./.test(file);
              })
            ]


    copy:
      images:
        files: [{
          expand:  true
          flatten: true
          cwd:     'blocks',
          src:     ['**/*.{png,jpg,jpeg,gif}', '!**/*_sprite.{png,jpg,jpeg,gif}']
          dest:    'publish'
        }]



    clean:
      pubimages:
        src: [
          "publish/*.{png,gif,jpg,jpeg}"
        ]


    imagemin:
      options:
        optimizationLevel: 5
      dist:
        files: [
          {
            expand: true
            cwd:    'publish/'
            src:    '**/*.{png,jpg,jpeg}'
            dest:   'publish/'
          },
          {
            expand: true
            cwd:    'tmp/'
            src:    '**/*.{png,jpg,jpeg}'
            dest:   'tmp/'
          },
          {
            expand: true
            cwd:    './'
            src:    '*.{png,jpg,jpeg,ico}'
            dest:   './'
          },
        ]

    concat:
      js:
        src: [
          '!lib/**/*.js',
          '!lib/jquery/*.js',
          'blocks/**/*.js'
        ]
        dest: 'publish/script.js'

      css:
        files:
          skinConcatConfigCssFiles

    uglify:
      dist:
        files:
          '<%= concat.js.dest %>': ['<%= concat.js.dest %>']


    jshint:
      files: [
        'blocks/**/*.js'
      ]
      options:
        curly:    true
        eqeqeq:   true
        eqnull:   true
        # quotmark: true
        undef:    true
        unused:   false

        browser:  true
        jquery:   true
        globals:
          console: true


    watch:
      options:
        livereload: false
        spawn:      false

      stylus:
        options:
          livereload: true
        files: [
          'blocks/**/*.styl'
        ]
        tasks: ['stylus', 'newer:concat:css','newer:autoprefixer']

      js:
        options:
          livereload: true
        files: [
          'lib/**/*.js',
          'blocks/**/*.js'
        ]
        tasks: ['newer:concat:js']

      grunt:
        options:
          livereload: true
        files: [
          'Gruntfile.coffee'  # auto reload gruntfile config
        ]
        tasks: ['newer:concat:js', 'stylus', 'newer:concat:css','newer:autoprefixer']

      twig:
        options:
          livereload: true
        files: ['*.twig', 'tmpl/**/*.twig']
        tasks: ['twigRender']

      images:
        files: [
          'blocks/**/*.{png,jpg,jpeg,gif}'
        ]
        tasks: ['copy:images']


    stylus:
      skinStylusConfig


    autoprefixer:
      no_dest:
        options:
          browsers: ['> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1', 'ie 9']       #default
        expand: true
        cwd:    'publish/'
        src:    '*.css'
        dest:   'publish/'


    cssmin: {
      my_target: {
        files: [{
          expand: true,
          cwd: 'publish/',
          src: ['style.css'],
          dest: 'publish/',
          ext: '.css'
        },
        {
          expand: true,
          cwd: 'publish/',
          src: ['style.ie.css'],
          dest: 'publish/',
          ext: '.ie.css'
        }
        ]
      }
    }

    open:
      mainpage:
        path: 'http://localhost:8000/'

    twigRender:
      your_target: {
        files : [
          {
            data: {},
            expand: true,
            cwd: './',
            src: ['*.twig'],
            dest: './',
            ext: '.html'
          }
        ]
      },


  @registerTask( 'default',    [ 'concat:js', 'stylus',  'newer:concat:css', 'autoprefixer', 'twigRender'])
  @registerTask( 'livereload', [ 'default', 'connect', 'open', 'watch' ])
  @registerTask( 'publish',    [ 'prepublish', 'uglify', 'cssmin'])
