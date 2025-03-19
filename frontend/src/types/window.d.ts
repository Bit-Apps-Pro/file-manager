import { type Edge, type Node, type SetViewport, type Viewport } from 'reactflow'

import { type FinderInstance } from 'elfinder'

interface AppStateType {
  flowState: {
    edges: Edge[]
    nodes: Node[]
    viewport: Viewport
    setViewport: SetViewport
  }
}

declare global {
  interface Window {
    appState: AppStateType
    elFinder: FinderInstance
  }
}

export {}
