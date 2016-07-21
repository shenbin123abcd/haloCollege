'use strict';
var gulp = require('gulp');
var gulpLoadPlugins = require('gulp-load-plugins');
var plugins = gulpLoadPlugins();
var sourcemaps = require('gulp-sourcemaps');
var browserSync = require('browser-sync').create();
var devip = require('dev-ip');
gulp.task('sass', function () {
    return gulp.src(['app/css/*.scss'])
        .pipe(sourcemaps.init())
        .pipe(plugins.sass({outputStyle: 'compact'}))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('app/css'));
});
gulp.task('haloIcon', function () {
    return gulp.src('app/css/lib/ux_*/*iconfont.*')
        .pipe(plugins.flatten())
        .pipe(gulp.dest('../../Public/Toutiao/css'))
});
gulp.task('vender', function () {
    return gulp.src('app/css/lib/ux_*/*iconfont.*')
        .pipe(plugins.flatten())
        .pipe(gulp.dest('../../Public/Toutiao/css'))
});

gulp.task('images', function () {
    return gulp.src(['app/images/**/*.{png,gif,jpg,svg，mp3,mp4}'])
    // .pipe(plugins.imagemin())
        .pipe(plugins.rev())
        .pipe(gulp.dest('../../Public/Toutiao/images'))
        .pipe(plugins.rev.manifest())
        .pipe(gulp.dest('tmp/images'))
});

gulp.task('build',['sass','images'], function () {
    var htmlFilter = plugins.filter('**/*.html',{restore: true});
    var jsFilter = plugins.filter('**/*.js',{restore: true});
    var jsVenderFilter = plugins.filter('**/vender.js',{restore: true});
    var cssFilter = plugins.filter('**/*.css',{restore: true});
    var manifestHtml = gulp.src("tmp/images/rev-manifest.json");
    var manifestCss = gulp.src("tmp/images/rev-manifest.json");
    var manifestJs = gulp.src("tmp/images/rev-manifest.json");
    return gulp.src('./app/detail.html')
        .pipe(plugins.useref())
        .pipe(jsFilter)
        .pipe(plugins.revReplace({manifest: manifestJs}))
        .pipe(plugins.cdnizer({
            defaultCDNBase: "__PUBLIC__/Toutiao",
            // defaultCDNBase: "http://7ktq5x.com1.z0.glb.clouddn.com/Wfc2016/supplier",
            allowRev: true,
            allowMin: true,
            matchers: [
                /(["'`])(.+?)(["'`])/gi,
            ],
            fallback: false,
            files: [
                'images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(plugins.babel({
            presets: ['es2015']
        }))
        .pipe(plugins.uglify())
        // .pipe(plugins.rev())
        .pipe(jsVenderFilter)
        .pipe(plugins.rev())
        .pipe(gulp.dest('../../Public/Toutiao'))
        .pipe(jsVenderFilter.restore)
        .pipe(gulp.dest('./tmp'))
        .pipe(jsFilter.restore)
        .pipe(cssFilter)
        .pipe(plugins.revReplace({manifest: manifestCss}))
        .pipe(plugins.cdnizer({
            defaultCDNBase: "__PUBLIC__/Toutiao",
            // defaultCDNBase: "http://7ktq5x.com1.z0.glb.clouddn.com/Wfc2016/supplier",
            allowRev: true,
            allowMin: true,
            relativeRoot: 'css',
            // matchers: [
            //     /(["'`\(])(.+?)(["'`\)])/gi,
            // ],
            // fallback: false,
            files: [
                'images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(plugins.autoprefixer({
            browsers:  ['> 0%'],
            cascade: false
        }))
        .pipe(plugins.csso())
        // .pipe(plugins.rev())
        .pipe(gulp.dest('./tmp'))
        .pipe(cssFilter.restore)
        .pipe(plugins.revReplace({
            replaceInExtensions: ['.js', '.css', '.html', '.ejs']
        }))
        .pipe(htmlFilter)
        .pipe(plugins.revReplace({manifest: manifestHtml}))
        .pipe(plugins.cdnizer({
            defaultCDNBase: "__PUBLIC__/Toutiao",
            // defaultCDNBase: "http://7ktq5x.com1.z0.glb.clouddn.com/Wfc2016/supplier",
            allowRev: true,
            allowMin: true,
            files: [
                'js/vender-*.js',
                {
                    file: 'js/**/*.js',
                    cdn: '../tmp/js/${ filename }'
                },
                // {
                //     file: 'css/**/*.css',
                //     cdn: '../tmp/css/${ filename }'
                // },
                // Thi
                // s file is on the default CDN, and will replaced with //my.cdn.host/base/js/app.js
                // 'css/**/*.css',
                // 'js/**/*.js',
                // 'js/hb.js',
                'images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(plugins.inject(gulp.src(['./tmp/js/main.js']), {
            starttag: '<!-- inject:main:{{ext}} -->',
            transform: function (filePath, file) {
                // console.log(file.contents)
                // return file contents as string
                return `<script>${file.contents.toString('utf8').replace(/\{/g,'{ ')}</script>`
            }
        }))
        .pipe(plugins.inject(gulp.src(['./tmp/css/main.css']), {
            starttag: '<!-- inject:main:{{ext}} -->',
            transform: function (filePath, file) {
                // console.log(file.contents)
                // return file contents as string
                return `<style>${file.contents.toString('utf8').replace(/\{/g,'{ ')}</style>`
            }
        }))
        // .pipe(plugins.htmlmin({
        //     removeComments: true,
        //     collapseWhitespace: true,
        //     conservativeCollapse: true,
        //     ignoreCustomFragments: [ /<%[\s\S]*?%>/, /<\?[\s\S]*?\?>/, /<include[\s\S]*?\/>/,/<else\/>/ ],
        //     minifyJS: false,
        //     minifyCSS: false,
        // }))
        .pipe(gulp.dest('../../Application/Home/View/Toutiao'))
        // .pipe(gulp.dest('dest'))
        .pipe(htmlFilter.restore)
});


gulp.task('clean', require('del').bind(null, [
    '../../Public/Toutiao/*',
    '../../Application/Home/View/Toutiao/*',
],{force:true}));


gulp.task('default',['clean'], function() {
    //gulp.start('build');
    gulp.start('build');
});


gulp.task('dev', ['clean'], function() {
    gulp.start('watch:dev');
});

gulp.task('browser-sync', function() {
    browserSync.init({
        open: false,
        ui: false,
        //notify: false,
        port: 19000,

        server: {
            baseDir: "./",
            middleware: function (req, res, next) {
                res.setHeader('Access-Control-Allow-Origin', '*');
                next();
            },
        }
    });

});


gulp.task('copy:view', function () {
    var htmlFilter = plugins.filter('**/*.html',{restore: true});
    return gulp
        .src(['app/*.html'])
        .pipe(htmlFilter)
        .pipe(plugins.cdnizer({
            defaultCDNBase: "http://"+devip()[0]+":19000/app",
            //defaultCDNBase: "../",
            allowRev: true,
            allowMin: true,
            files: [
                // Thi
                // s file is on the default CDN, and will replaced with //my.cdn.host/base/js/app.js
                'css/**/*.css',
                {
                    file: 'js/**/*.js',
                    cdn: "http://"+devip()[0]+":19000/tmp/js/${ filename }"
                },
                'images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(gulp.dest('../../Application/Home/View/Toutiao'))

        ;
});
gulp.task('copy:js', function () {
    return gulp
        .src(['app/js/*.js'])
        .pipe(plugins.cached('myjs'))
        .pipe(plugins.cdnizer({
            defaultCDNBase: "http://"+devip()[0]+":19000/app",
            //defaultCDNBase: "../",
            allowRev: true,
            allowMin: true,
            matchers: [
                /(["'`])(.+?)(["'`])/gi,
            ],
            fallback: false,
            files: [
                'images/**/*',
            ]
        }))
        .pipe(plugins.babel({
            presets: ['es2015']
        }))
        .pipe(gulp.dest('tmp/js'))
        ;
});



gulp.task("watch:dev", ['browser-sync','copy:view','sass','copy:js'], function(){
    gulp.watch(['app/*.html'],function(event) {
        //console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
        gulp.start('copy:view');

    });
    gulp.watch(['app/js/*.js'],function(event) {
        //console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
        gulp.start('copy:js');

    });

});

