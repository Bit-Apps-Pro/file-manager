import css from './SpinnerLoader.module.scss'

interface SpinnerLoaderType {
  size: number
  stroke?: number
  className?: string
  color?: 'light' | 'primary'
}

export default function SpinnerLoader({
  size,
  stroke = 3,
  className = undefined,
  color = 'light'
}: SpinnerLoaderType) {
  return (
    <div className={`${className}`} style={{ height: size, width: size }} data-testid="spinnerLoader">
      <div
        className={`${css.loading} ${color !== 'light' && css[color]}`}
        style={{ height: size, width: size, borderWidth: stroke }}
      />
    </div>
  )
}
