/**
 * This method only accepts one argument.
 *
 * Performs queries using mongoose-simpledb
 *
 * (id)
 *
 * @param {simpledb} db
 * @param {callback} cb
 */
function deleteMethod(db, cb) {

    // Getting the ID
    let id = process.argv.shift();

    // Finding the record with the provided ID then removing it.
    db.Orders.find({ _id : id }).remove( (err, result) => {

        // Checking if it was successful and sending results.
        if(result.ok !== 1)
            return cb(err, `Unsuccessful at deleting ${id}`);
        else
            return cb(err, `Successful at deleting ${id}`);
    });
}

// Exporting module.
module.exports = deleteMethod;