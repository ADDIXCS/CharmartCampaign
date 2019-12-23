'use strict'

module.exports = (grunt) ->
  # grunt-から始まるモジュールを読み込む
  require('matchdep').filterDev('grunt-*').forEach grunt.loadNpmTasks

  # フォルダ名の設定
  dir =
    src: 'app/Asset'
    dist: 'webroot'

  # 各タスクの設定
  grunt.initConfig
    # 変数の設定
    pkg: grunt.file.readJSON 'package.json'
    dir: dir
    # Bowerのインストール、レイアウト
    bower:
      install:
        options:
          copy: true
          targetDir: '<%= dir.dist %>/vendor'
          layout: 'byComponent'
          install: true
          verbose: true
          cleanTargetDir: true
    # フォルダ内のクリア
    clean:
      dist: [
        '<%= dir.dist %>/css/*'
        '<%= dir.dist %>/js/*'
        '!<%= dir.dist %>/css/cake.generic.css'
        '!<%= dir.dist %>/js/empty'
      ]
    # coffee scriptの文法チェック
    coffeelint:
      dist: '<%= dir.src %>/js/*.coffee'
      watch: ''
      options:
        max_line_length:
          level: 'ignore'
    # coffee scriptのコンパイル
    coffee:
      dist:
        expand: true
        cwd: '<%= dir.src %>/js'
        src: '*.coffee'
        dest: '<%= dir.dist %>/js'
        ext: '.js'
      watch:
        expand: true
        dest: '<%= dir.dist %>'
        ext: '.js'
        rename: (dest, src) ->
          return dest + src.replace(dir.src, '')
      options:
        # 無名関数でのラップをしない
        bare: true
    # compassのコンパイル
    compass:
      dist: {}
      watch:
        options: {}
      options:
        sassDir: '<%= dir.src %>/css'
        cssDir: '<%= dir.dist %>/css'
        outputStyle: 'nested'
        importPath: 'bower_components/bootstrap-sass-official/assets/stylesheets/bootstrap'
    # ファイル監視
    watch:
      options:
        spawn: false
      coffee:
        files: ['<%= dir.src %>/js/*.coffee']
        tasks: ['coffeelint:watch', 'coffee:watch']
      compass:
        files: ['<%= dir.src %>/css/*.{sass,scss}']
        tasks: ['compass:watch']

  # 変更されたファイルのみをコンパイルするための設定
  grunt.event.on 'watch', (action, filepath) ->
    if filepath.indexOf('.coffee') > -1
      grunt.config.set 'coffeelint.watch', filepath
      grunt.config.set 'coffee.watch.src', filepath
    else if filepath.indexOf('.sass') > -1 or filepath.indexOf('.scss') > -1
      filename = filepath.replace(dir.src + '/css/', '')
      if filename.indexOf('_') isnt 0
        grunt.config.set 'compass.watch.options.specify', filepath
      else
        grunt.config.set 'compass.watch.options.specify', ''

  # 開発時用
  grunt.registerTask 'default', [
    'build'
    'watch'
  ]
  # 本番用
  grunt.registerTask 'build', [
    'bower:install'
    'clean:dist'
    'coffeelint:dist'
    'coffee:dist'
    'compass:dist'
  ]

