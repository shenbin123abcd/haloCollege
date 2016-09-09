
export function initSeats(data) {
    // console.log(data)
    return {
        type: 'INIT_SEATS',
        items: data,
    }
}
export function setSeatsStatus(clickable=false) {
    // console.log(data)
    return {
        type: 'SET_SEATS_STATUS',
        clickable: clickable,
    }
}
export function destroySeats() {
    // console.log(data)
    return {
        type: 'DESTROY_SEATS',
        items: null,
    }
}

export function selectSeat(seat) {
    // console.log(seat)
    return {
        type: 'SELECT_SEAT',
        selectedItem: seat,
    }
}

export function selectRandomSeat(seat) {
    // console.log(seat)
    return {
        type: 'SELECT_RANDOM_SEAT',
    }
}

