module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    uglify: {
      options: {
        banner: '/*! build <%= grunt.template.today("yyyy-mm-dd HH:mm:ss") %> */\n/*! rbot.js */\n/*! Copyright Francois Lajoie */\n/*! MIT License */\n'
      },
      my_target: {
        files: {
          'public/assets/js/rbot.min.js': ['resources/js/rbot.js']
        }
      }
    },

  
    // copy: {
    //   build: {
    //     files: [
    //       {
    //         cwd: 'src/boilerplate',     // set working folder / root to copy
    //         src: '**/*',                // copy all files and subfolders
    //         dest: 'build/boilerplate',  // destination folder
    //         expand: true                // required when using cwd
    //       },
    //     ],
    //   },
    // },


    less: {
      development: {},
      production: {
        options: {
          cleancss: true,
        },
        files: {
          "public/assets/css/rbot.css": "resources/less/rbot.less",
        }
      }
    },

    concat: {
      options: {
        separator: ';',
      },
      dist: {
        src: ['resources/js/angular.min.js'],
        dest: 'public/assets/js/libs.js',
      },
    },

    // clean: {
    //   less: [""],
    // },

    watch: {
      scripts: {
        files: ['resources/js/*.js', 'resources/less/*.less'],
        tasks: ['default'],
        options: {
          spawn: false,
        },
      },
    },
  });

  // Load the plugins
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-less');
  //grunt.loadNpmTasks('grunt-contrib-copy');
  //grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task (AKA BUILD)
  grunt.registerTask('default', ['less', 'concat', 'uglify']);
};