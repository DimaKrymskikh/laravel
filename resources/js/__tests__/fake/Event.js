// Имитируем объект Event
export const eventCurrentTargetClassListContainsFalse = {
    currentTarget: {
        classList: {
            // Для теста нам нужен false (класса 'disabled' нет в currentTarget)
            contains: (p) => false
        }
    }
};

export const eventTargetClassListContainsFalseAndGetAttribute8 = {
    target: {
        classList: {
            // Для теста нам нужен false (класса 'disabled' нет в target)
            contains: (p) => false
        },
        getAttribute: (p) => 8
    }
};
