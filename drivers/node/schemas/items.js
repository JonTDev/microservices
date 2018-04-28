const ObjectId = require('mongoose-simpledb').Types.ObjectId;

exports.schema = {
    name : String,
    price : String,
    sku : String,
    quantity : Number
};