module.exports = (grunt) ->
  'use strict'
  require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

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
        src: [
          'lib/**/*.css',
          'blocks/**/*.css'
        ]
        dest: 'publish/style.css'

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
        tasks: ['newer:stylus:dev', 'newer:concat:css','newer:autoprefixer']

      js:
        options:
          livereload: true
        files: [
          'lib/**/*.js',
          'blocks/**/*.js',
          'Gruntfile.coffee'  # auto reload gruntfile config
        ]
        tasks: ['newer:concat:js']

      html:
        options:
          livereload: true
        files: [
          '*.html'
        ]

      images:
        files: [
          'blocks/**/*.{png,jpg,jpeg,gif}'
        ]
        tasks: ['copy:images']


    stylus:
      options:
        compress: false
        paths: ['blocks/']
        import: [
          'config.styl',
          'mixins/i-mixins__clearfix.styl'
        ]

      dev:
        expand: true
        cwd:    'blocks/'
        src:    [
          '**/*.styl',
          '!mixins/i-mixins__clearfix.styl',
          '!config.styl',
        ]
        dest: 'blocks'
        ext:  '.css'

  
    autoprefixer:
      no_dest:
        src: 'publish/style.css'
        options:
          browsers: ['> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1', 'ie 9']       #default


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
        path: 'http://localhost:8000/';


  
  @registerTask( 'default',    [ 'concat:js', 'newer:stylus:dev',  'newer:concat:css', 'autoprefixer'])
  @registerTask( 'livereload', [ 'default', 'connect', 'open', 'watch' ])
  @registerTask( 'publish',    [ 'prepublish', 'uglify', 'cssmin'])
