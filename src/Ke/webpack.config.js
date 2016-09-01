var webpack = require("webpack");

module.exports = {
    entry: __dirname+"/app/entry.js",
    output: {
      path: __dirname+"/../../Public/Ke",
      filename: "bundle.js",
      publicPath: '/Public/Ke/',
    },
    // watch: true,
    // devtool: "source-map",
    module: {
        loaders: [
            //{
            //    test: /\.css$/,
            //    loader: "style!css"
            //},
            //{
            //    test: /\.scss$/,
            //    loaders: ["style", "css",'postcss',"sass"]
            //},
            //{ test: /\.jade$/, loader: "jade-html" },
          {
              test: /\.jsx?$/,
              exclude: /(node_modules|bower_components)/,
              loader: 'babel',
              query: {
                  presets: ['es2015']
              }
          },
          //{
          //  test: /\.(png|jpg|jpeg|gif|woff)$/,
          //  loader: 'url-loader'
          //},
          {
            test: /\.(png|jpg|jpeg|gif|woff)$/,
            loader: 'file',
            query: {
              name: '[path][name].[ext]?[hash]',
              // name: '[path][name].[ext]?[hash]',
            }
          },
        ]
    },
    //postcss: function () {
    //    return {
    //        defaults: [ autoprefixer],
    //        cleaner:  [autoprefixer({ browsers: ["> 0%"] })]
    //    };
    //},
    externals: {
        // require("jquery") is external and available
        //  on the global var jQuery
        "jquery": "jQuery",
        //"hb": "hb",
    },
    plugins: [
       new webpack.optimize.UglifyJsPlugin({
           compress: {
               warnings: false,
           },
           output: {
               comments: false,
           },
       }),
    ]
};
