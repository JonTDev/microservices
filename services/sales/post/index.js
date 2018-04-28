/**
 * Must match the exact number of columns in the collection.
 * Only provide the values do not provide the columns as
 * It is done for you.
 * Please keep it in the order listed below as that is how
 * the record willl be inserted.
 *
 * (portal, order, invoice, email)
 *
 * @param db
 * @param cb
 */
function insertMethod(db, cb) {

    // Adding columns to an array.
    let keys = Object.keys(db.Orders.schema.obj);

    // Preparing to get the values.
    let argv = {};

    // Asynchronously iterating through the keys.
    db.forEachAsync(keys, (k, v, done) => {

        // Setting Value with the column
        argv[v] = process.argv.shift();

        // Checking the newly set value is not empty or null
        if(!argv[v])

            // If it is preparing and sending response.
            return cb(null, 'Bad Request');

        // Checking if this is the last iteration.
        if(done() === false) {

            // Creating a new record with the provided arguments.
            let newOrder = new db.Orders(argv);

            // Saving the new record.
            newOrder.save((err) => {

                // Returning the results.
                // If an error is detected Success will never be read.
                return cb(err, 'Success');
            })
        }
    });
}

// Exporting module.
module.exports = insertMethod;