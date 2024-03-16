import { atom } from 'jotai'

export default function atomWithBroadcast<T>(key: string, initialValue: T) {
  const baseAtom = atom(initialValue)
  const listeners = new Set<(event: MessageEvent<any>) => void>() // eslint-disable-line @typescript-eslint/no-explicit-any
  const channel = new BroadcastChannel(key)
  channel.onmessage = event => {
    listeners.forEach(l => l(event))
  }

  const broadcastAtom = atom<T, [update: { isEvent: boolean; value: T | ((value: T) => T) }], void>(
    get => get(baseAtom),
    (get, set, update) => {
      set(baseAtom, update.value)

      if (!update.isEvent) {
        channel.postMessage(get(baseAtom))
      }
    }
  )

  broadcastAtom.onMount = setAtom => {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const listener = (event: MessageEvent<any>) => {
      setAtom({ isEvent: true, value: event.data })
    }
    listeners.add(listener)
    return () => {
      listeners.delete(listener)
    }
  }

  const returnedAtom = atom<T, [update: T | ((value: T) => T)], void>(
    get => get(broadcastAtom),
    (_get, set, update) => {
      set(broadcastAtom, { isEvent: false, value: update })
    }
  )

  return returnedAtom
}
