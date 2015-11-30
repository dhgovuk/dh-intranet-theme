module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-img');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-jshint');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            dist: {
                options: {},
                files: {
                    'build/css/main.min.css': [
                        'assets/scss/main.scss',
                    ],
                    'build/css/wp-admin.min.css': [
                        'assets/scss/wordpress/wp-admin.scss',
                    ],
                },
            },
        },

        uglify: {
            dist: {
                options: {
                    preserveComments: 'some',
                    compress: false,
                    sourceMap: 'build/js/main.min.js.map',
                    sourceMappingURL: 'main.min.js.map',
                    sourceMapRoot: '../',
                },
                files: {
                    'build/js/main.min.js': [
                        'assets/js/plugins/*.js',
                        'assets/js/main.js',
                    ],
                },
            },
        },

        img: {
            dist: {
                src: 'assets/img',
            },
        },

        _watch: {
            css: {
                files: ['assets/scss/**/*.scss', 'assets/scss/*.scss'],
                tasks: ['sass'],
            },
            js: {
                files: ['assets/js/**/*.js'],
                tasks: ['uglify'],
            },
        },
    })

    grunt.registerTask('bower-install', 'Installs bower deps', function () {
        var done = this.async()
          , bower = require('bower')

        bower.commands.install().on('end', function () {
            done()
        })
    })

    grunt.renameTask('watch', '_watch')
    grunt.registerTask('watch', [
        'default',
        '_watch'
    ])

    grunt.registerTask('default', [
        'bower-install',
        'uglify',
        'sass'
    ])
}
