Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'daily-checks',
      path: '/daily-checks',
      component: require('./components/Index'),
    },
    {
      name: 'daily-checks.new',
      path: '/daily-checks/shops/:shop/:date/new',
      component: require('./components/NewDailyCheck'),
      props: true
    },
  ])
})
