export default function generateFlowsData(totalFlows: number) {
  const createFlows = []

  for (let i = 1; i <= totalFlows; i += 1) {
    if (i % 5 === 0) {
      createFlows.push({
        id: i,
        icon: 'icon link',
        title: `Group Title ${i}`,
        subtitle: 'Group',
        type: 'flow-group',
        flows: [
          {
            id: '1',
            icon: 'icon link',
            title: 'Flow group Title 1',
            count: 20,
            type: 'flow'
          },
          {
            id: '2',
            icon: 'icon link',
            title: 'Flow group Title 2',
            count: 20,
            type: 'flow'
          },
          {
            id: '3',
            icon: 'icon link',
            title: 'Flow group Title 3',
            count: 20,
            type: 'flow'
          }
        ]
      })
    } else {
      createFlows.push({
        id: i,
        icon: 'icon link',
        title: `Flow Title ${i}`,
        count: 20,
        type: 'flow'
      })
    }
  }
  return JSON.stringify(createFlows)
}
