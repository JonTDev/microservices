/**
 * Using the mongoose-simpledb class to perform queries to mongodb.
 *
 * Takes four parameters only values
 * (name, price, sku, quantity)
 *
 * @param {simpledb} db
 * @param {callback} cb
 */
function createMethod(db, cb) {
    // Getting the schemas column names.
    let keys = Object.keys(db.Items.schema.obj);

    // Preping the values to insert.
    let argv = {};

    // Asynchronously iterating throguh the column names.
    db.forEachAsync(keys, (k, v, done) => {

        // Getting the values from the arguments and preparing the insert.
        argv[v] = process.argv.shift();

        // Are there more arguments that column names?
        if(!argv[v])
            // If so then callback with bad request in the error section.
            return cb('Bad Request');

        // Are we finished iterating?
        if(done() === false) {
            // Yes we are.
            // Creating a new record and passing the
            // column names and values.
            let newItem = new db.Items(argv);

            // Saving the new record.
            newItem.save((err) => {

                // returning the results.
                return cb(err, 'Success');
            })
        }
    });
}


module.exports = createMethod;