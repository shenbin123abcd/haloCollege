'use strict';
var gulp = require('gulp');
var gulpLoadPlugins = require('gulp-load-plugins');
var plugins = gulpLoadPlugins();
var sourcemaps = require('gulp-sourcemaps');

var webpack = require("webpack");
var webpackStream = require('webpack-stream');

var browserSync = require('browser-sync').create();
var devip = require('dev-ip');
var appConfig = {
    themeSrc:'./',
    themeDist:'../../Public/Ke',
    themeViewDist:'../../Application/Ke/View/Index',
    port:9201,
};

//console.log(devip());


gulp.task('sass', function () {
    return gulp.src([`${appConfig.themeSrc}/css/*.scss`])
        .pipe(sourcemaps.init())
        .pipe(plugins.sass({outputStyle: 'compact'}).on('error', plugins.sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(`${appConfig.themeSrc}/css`));
});


gulp.task('copy:css',['sass'], function () {
    return gulp.src([`${appConfig.themeSrc}/css/**/*.{css,map}`])
        .pipe(gulp.dest(`${appConfig.themeDist}/css`));
});


gulp.task('copy:js', function () {
    return gulp
        .src([`${appConfig.themeSrc}/js/*.js`])
        .pipe(plugins.cached('myjs'))
        .pipe(plugins.cdnizer({
            defaultCDNBase: `/Public/Ke`,
            //defaultCDNBase: "../",
            allowRev: true,
            allowMin: true,
            matchers: [
                /(["'`])(.+?)(["'`])/gi,
            ],
            fallback: false,
            files: [
                '/images/**/*',
            ]
        }))
        .pipe(plugins.babel({
            presets: ['es2015']
        }))
        .on('error', function(e) {
            console.error(e);
            this.emit('end');
        })
        .pipe(gulp.dest(`${appConfig.themeDist}/js`))
        ;
});


gulp.task('images', function () {
    return gulp.src([`${appConfig.themeSrc}/images/**/*.{png,gif,jpg,svg,mp3,mp4}`])
    // .pipe(plugins.imagemin())
        .pipe(plugins.rev())
        .pipe(gulp.dest(`${appConfig.themeDist}/images`))
        .pipe(plugins.rev.manifest())
        .pipe(gulp.dest('tmp/images'))
});
gulp.task('images:dev', function () {
    return gulp.src([`${appConfig.themeSrc}/images/**/*.{png,gif,jpg,svg,mp3,mp4}`])
    // .pipe(plugins.imagemin())
        .pipe(gulp.dest(`${appConfig.themeDist}/images`))
});


gulp.task('fakedata', function (cb) {
    exec('node fakedata/fakeapi.js', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
        cb(err);
    });
})
gulp.task('build',['sass','images','webpack'], function () {
    var htmlFilter = plugins.filter('**/*.html',{restore: true});
    var jsFilter = plugins.filter('**/*.js',{restore: true});
    var jsAppFilter = plugins.filter(`**/hb.drag.js`,{restore: true});
    var jsVenderFilter = plugins.filter('**/vender.js',{restore: true});
    var cssFilter = plugins.filter('**/*.css',{restore: true});
    var manifestHtml = gulp.src("tmp/images/rev-manifest.json");
    var manifestCss = gulp.src("tmp/images/rev-manifest.json");
    var manifestJs = gulp.src("tmp/images/rev-manifest.json");
    return gulp.src('app/index.html')
        .pipe(plugins.useref())
        .pipe(jsFilter)
        .pipe(plugins.revReplace({manifest: manifestJs}))
        .pipe(plugins.cdnizer({
            defaultCDNBase: `/Public/Ke`,
            //defaultCDNBase: "../",
            allowRev: true,
            allowMin: true,
            matchers: [
                /(["'`])(.+?)(["'`])/gi,
            ],
            fallback: false,
            files: [
                '/images/**/*',
            ]
        }))

        .pipe(plugins.rev())
        .pipe(plugins.babel({
            presets: ['es2015']
        }))
        .pipe(plugins.uglify())
        .pipe(gulp.dest(`${appConfig.themeDist}`))
        .pipe(jsFilter.restore)
        .pipe(cssFilter)
        .pipe(plugins.revReplace({manifest: manifestCss}))
        .pipe(plugins.cdnizer({
            defaultCDNBase: `/Public/Ke`,
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
        .pipe(plugins.rev())
        // .pipe(gulp.dest('./tmp'))
        .pipe(gulp.dest(`${appConfig.themeDist}`))
        .pipe(cssFilter.restore)
        .pipe(plugins.revReplace({
            replaceInExtensions: ['.js', '.css', '.html', '.ejs']
        }))
        .pipe(htmlFilter)
        .pipe(plugins.revReplace({manifest: manifestHtml}))
        .pipe(plugins.cdnizer({
            defaultCDNBase: "__PUBLIC__/Ke",
            // defaultCDNBase: "http://7ktq5x.com1.z0.glb.clouddn.com/Wfc2016/supplier",
            allowRev: true,
            allowMin: true,
            files: [
                // 'js/vender-*.js',
                // 'css/vender-*.js',
                // {
                //     file: 'js/**/*.js',
                //     cdn: '../tmp/js/${ filename }'
                // },
                // {
                //     file: 'css/**/*.css',
                //     cdn: '../tmp/css/${ filename }'
                // },
                // Thi
                // s file is on the default CDN, and will replaced with //my.cdn.host/base/js/app.js
                'css/**/*.css',
                'js/**/*.js',
                // 'js/hb.js',
                // 'images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(plugins.htmlmin({
            removeComments: true,
            collapseWhitespace: true,
            conservativeCollapse: true,
            ignoreCustomFragments: [ /<%[\s\S]*?%>/, /<\?[\s\S]*?\?>/, /<include[\s\S]*?\/>/,/<else\/>/ ],
            minifyJS: false,
            minifyCSS: false,
        }))
        .pipe(gulp.dest(`${appConfig.themeViewDist}`))
        // .pipe(gulp.dest('dest'))
        .pipe(htmlFilter.restore)
});

gulp.task('copy:view', ['copy:css'],function () {
    var htmlFilter = plugins.filter(`**/*.html`,{restore: true});
    return gulp
        .src([`${appConfig.themeSrc}app/**/*.html`])
        .pipe(htmlFilter)
        .pipe(plugins.cdnizer({
            defaultCDNBase: `/`,
            //defaultCDNBase: "../",
            allowRev: true,
            allowMin: true,
            relativeRoot: 'app/app/app',
            files: [
                // Thi
                // s file is on the default CDN, and will replaced with //my.cdn.host/base/js/app.js
                'Public/Ke/**/*.js',
                //'public/images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(plugins.cdnizer({
            defaultCDNBase: `__PUBLIC__/Ke`,
            //defaultCDNBase: "../",
            allowRev: true,
            allowMin: true,
            relativeRoot: 'app',
            files: [
                // Thi
                // s file is on the default CDN, and will replaced with //my.cdn.host/base/js/app.js
                'css/**/*.css',
                'js/**/*.js',
                //'public/images/**/*.{jpg,png,mp3,mp4}',
            ]
        }))
        .pipe(gulp.dest(`${appConfig.themeViewDist}`))
        ;
});

gulp.task('clean', require('del').bind(null, [
    `${appConfig.themeDist}/*`,
    `${appConfig.themeViewDist}/*`,
],{force:true}));

gulp.task('dev', ['clean'], function() {
    gulp.start('watch:dev');
});
gulp.task('default',['clean'], function() {
    //gulp.start('build');
    gulp.start('build');
});

gulp.task("watch:dev", ['copy:view','copy:css','copy:js','images:dev'], function(){
    gulp.watch([`${appConfig.themeSrc}/css/**/*.scss`], ['copy:css']);
    gulp.watch([`${appConfig.themeSrc}/js/**/*.js`], ['copy:js']);
    gulp.watch([`${appConfig.themeSrc}/**/*.html`], ['copy:view']);
    gulp.watch([`${appConfig.themeSrc}/images/**/*.*`], ['images:dev']);
    gulp.start('webpack:dev');
});

gulp.task("webpack:dev", function(callback) {
    var webpackConfig=require('./webpack.config.dev.js');
    return webpack( webpackConfig, function(err, stats) {
        if(err) throw new plugins.util.PluginError("webpack", err);
        plugins.util.log("[webpack]", stats.toString({
            // output options
        }));
        //gutil.log("[webpack]", "Gonna sit around and watch for file changes. CTRL^C to kill me");
        // callback();
    });
});

gulp.task("webpack:ewteert", function(callback) {
    var webpackConfig=require('./webpack.config.js');
    return gulp.src('app/entry.js')
        .pipe(webpackStream( webpackConfig ))
        .pipe(gulp.dest(`tmp`))
        ;
});
gulp.task("webpack", function(callback) {
    var webpackConfig=require('./webpack.config.js');
    return webpack( webpackConfig, function(err, stats) {
        if(err) throw new plugins.util.PluginError("webpack", err);
        plugins.util.log("[webpack]", stats.toString({
            // output options
        }));
        //gutil.log("[webpack]", "Gonna sit around and watch for file changes. CTRL^C to kill me");
        callback();
    });
});
