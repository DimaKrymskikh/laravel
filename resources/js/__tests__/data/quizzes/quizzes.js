
export const quizzes = [{
    id: 1,
    title: "Арифметические операции",
    description: "Изучаются арифметические операции.",
    lead_time: 5,
    status: {
        name: "в работе",
        style: "status-sky",
        isEditable: true,
        colorSvg: "red",
        titleSvg: "Опрос имеет статус 'в работе', нельзя перевести в статус 'утверждён'"
    }
}, {
    id: 2,
    title: "Прямые на плоскости",
    description: "Изучаются прямые на плоскости.",
    lead_time: 15,
    status: {
        name: "готов",
        style: "status-yellow",
        isEditable: true,
        colorSvg: "yellow",
        titleSvg: "Опрос имеет статус 'готов', хотите перевести опрос в статус 'утверждён'?"
    }
}, {
    id: 3,
    title: "Плоскости",
    description: "Изучаются плоскости.",
    lead_time: 15,
    status: {
        name: "удалён",
        style: "status-gray",
        isEditable: false,
        colorSvg: "gray",
        titleSvg: "Опрос имеет статус 'удалён', нельзя перевести в статус 'утверждён'"
    }
}, {
    id: 4,
    title: "Кривые второго порядка",
    description: "Изучаются кривые второго порядка на плоскости.",
    lead_time: 15,
    status: {
        name: "утверждён",
        style: "status-green",
        isEditable: false,
        colorSvg: "green",
        titleSvg: "Опрос имеет статус 'утверждён', хотите отменить этот статус?"
    }
}];

export const quizWithoutItems = { ...quizzes[0], quiz_items: [] };

export const quizWithItems = { ...quizzes[0], quiz_items: [{
    id: 1,
    description: "2 * 2 = ?",
    status: {
        name: "в работе",
        style: "status-sky",
        isEditable: true
    },
    quiz_id: quizzes[0].id
}] };
