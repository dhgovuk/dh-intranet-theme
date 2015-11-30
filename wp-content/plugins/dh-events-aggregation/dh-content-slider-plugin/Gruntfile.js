module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        copy: {
            dist: {
                files: [
                    {
                        src: [
                            'bower_components/jsrender/jsrender.min.js',
                        ],
                        dest: 'build/',
                    },
                ],
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

    grunt.registerTask('default', [
        'bower-install',
        'copy',
    ])
}
