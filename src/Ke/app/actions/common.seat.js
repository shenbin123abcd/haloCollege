
export function initSeats(data) {
    // console.log(data)
    return {
        type: 'INIT_SEATS',
        items: data,
    }
}
export function destroySeats() {
    // console.log(data)
    return {
        type: 'DESTROY_SEATS',
        items: null,
    }
}