Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'critical-cases-p1',
            path: '/critical-cases-p1',
            component: require('./components/Index'),
        },
        {
            name: 'critical-cases-p1.new',
            path: '/critical-cases-p1/shops/:shop/:date/new',
            component: require('./components/NewDailyCheck'),
            props: true
        },
    ])
})
