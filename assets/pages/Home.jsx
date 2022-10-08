import React from "react"
import InfiniteScroll from "react-infinite-scroller"
import { Link } from "react-router-dom"
import Spinner from "../components/Spinner"

class Home extends React.Component {
  state = {
    page: 1,
    films: [],
    isLoadingFilms: false,
    hasMoreFilms: true,
    configuration: null,
  }

  constructor(props) {
    super(props)

    this.state.configuration = props.configuration

    this.loadFilms = this.loadFilms.bind(this)
  }

  loadFilms() {
    if (this.state.isLoadingFilms) {
      return
    }
    this.setState({ isLoadingFilms: true })
    fetch("/api/movie-discover/" + this.state.page)
      .then((res) => res.json())
      .then((res) => {
        this.setState({
          films: [...this.state.films, ...res.response.results],
          isLoadingFilms: false,
          page: this.state.page + 1,
        })
      })
  }

  render() {
    return (
      <>
        <InfiniteScroll
          className="mt-8 px-6"
          hasMore={this.state.page <= 1000}
          loadMore={this.loadFilms}
          loader={
            <div key="loader" className="text-center mb-4">
              <Spinner className="w-10 h-10" />
            </div>
          }
        >
          <ul className="container mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-x-6 gap-y-6 mb-4">
            {this.state.films.map((film) => (
              <li
                key={film.id}
                className="sm:max-w-sm rounded-lg border border-gray-200 shadow-md"
              >
                <Link to={"film/" + film.id} className="flex sm:block relative">
                  <picture>
                    <source
                      media="(max-width: 640px)"
                      srcSet={
                        this.state.configuration.images.secure_base_url +
                        (this.state.configuration.images.poster_sizes.includes(
                          "w185",
                        )
                          ? "w185"
                          : "original") +
                        film.poster_path
                      }
                    />
                    <img
                      className="rounded-l-lg sm:rounded-bl-none sm:rounded-t-lg max-w-[92px] sm:max-w-full"
                      src={
                        this.state.configuration.images.secure_base_url +
                        (this.state.configuration.images.poster_sizes.includes(
                          "w500",
                        )
                          ? "w500"
                          : "original") +
                        film.poster_path
                      }
                      alt=""
                    />
                  </picture>
                  <div className="absolute -top-2 -left-2 flex space-x-2">
                    <div
                      className={
                        "px-2 rounded-lg text-white " +
                        (film.vote_average >= 7
                          ? "bg-green-500"
                          : film.vote_average < 5
                          ? "bg-red-500"
                          : "bg-gray-500")
                      }
                    >
                      {film.vote_average.toFixed(1)}
                    </div>
                    <div
                      className={
                        "px-2 rounded-lg text-white hidden sm:block " +
                        {
                          1: "bg-green-400",
                          2: "bg-green-500",
                          3: "bg-yellow-400",
                          4: "bg-yellow-500",
                          5: "bg-orange-500",
                          6: "bg-red-500",
                        }[film.language]
                      }
                    >
                      {
                        {
                          1: "A1",
                          2: "A2",
                          3: "B1",
                          4: "B2",
                          5: "C1",
                          6: "C2",
                        }[film.language]
                      }
                    </div>
                  </div>
                  <div className="px-5 py-3 flex flex-col justify-between">
                    <p className="font-bold text-gray-900 sm:text-center mb-2 sm:mb-0">
                      {film.title}
                      <span
                        className={
                          "ml-2 inline-block font-normal px-2 rounded-lg text-white sm:hidden " +
                          {
                            1: "bg-green-400",
                            2: "bg-green-500",
                            3: "bg-yellow-400",
                            4: "bg-yellow-500",
                            5: "bg-orange-500",
                            6: "bg-red-500",
                          }[film.language]
                        }
                      >
                        {
                          {
                            1: "A1",
                            2: "A2",
                            3: "B1",
                            4: "B2",
                            5: "C1",
                            6: "C2",
                          }[film.language]
                        }
                      </span>
                    </p>
                    <p className="text-gray-700 line-clamp-2 sm:hidden">
                      {film.overview}
                    </p>
                  </div>
                </Link>
              </li>
            ))}
          </ul>
        </InfiniteScroll>
      </>
    )
  }
}

export default Home
