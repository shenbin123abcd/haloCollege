
export function initSeats(data) {
    // console.log(data)
    return {
        type: 'INIT_SEATS',
        items: data,
    }
}